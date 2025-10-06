<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\Suspenseutilization;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateInvoiceSettlementDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:update-settlement-dates {--dry-run : Show what would be updated without making changes} {--chunk=1000 : Number of invoices to process at once} {--commit-chunk=5000 : Number of invoices to commit at once} {--debug : Show detailed debugging information} {--test : Test mode - process only first 10 invoices}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update settlement_date column in invoices table based on latest Suspenseutilization created_at date for PAID invoices only';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');
        $isDebug = $this->option('debug');
        $isTest = $this->option('test');
        $chunkSize = (int) $this->option('chunk');
        $commitChunkSize = (int) $this->option('commit-chunk');
        
        if ($isDryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
        }

        if ($isDebug) {
            $this->info('🐛 DEBUG MODE - Detailed information will be shown');
        }

        if ($isTest) {
            $this->info('🧪 TEST MODE - Processing only first 10 invoices');
        }

        $this->info('🚀 Starting settlement date update process...');
        $this->info("📦 Processing in chunks of {$chunkSize} invoices");
        $this->info("💾 Committing every {$commitChunkSize} updates");
        $this->info("💰 Only processing invoices with status 'PAID'");

        // First, let's check what we're actually dealing with
        $this->info('🔍 Analyzing current data state...');
        
        $totalPaidInvoices = Invoice::where('status', 'PAID')->count();
        $paidWithSuspense = Invoice::where('status', 'PAID')->whereHas('receipts')->count();
        $paidWithSettlementDate = Invoice::where('status', 'PAID')->whereNotNull('settlement_date')->count();
        $paidWithSettlementDateAndSuspense = Invoice::where('status', 'PAID')
            ->whereNotNull('settlement_date')
            ->whereHas('receipts')
            ->count();
        
        $this->info("📊 Total PAID invoices: {$totalPaidInvoices}");
        $this->info("📊 PAID invoices with Suspenseutilization: {$paidWithSuspense}");
        $this->info("📊 PAID invoices with settlement_date: {$paidWithSettlementDate}");
        $this->info("📊 PAID invoices with both settlement_date and Suspenseutilization: {$paidWithSettlementDateAndSuspense}");

        // Get PAID invoices that have Suspenseutilization records but don't have settlement_date set yet
        $invoicesQuery = Invoice::where('status', 'PAID')
            ->whereHas('receipts')
            ->where(function($query) {
                $query->whereNull('settlement_date')
                      ->orWhere('settlement_date', '')
                      ->orWhere('settlement_date', '0000-00-00 00:00:00');
            });
        
        if ($isTest) {
            $invoicesQuery->limit(10);
        }
        
        $totalInvoices = $invoicesQuery->count();
        
        if ($totalInvoices === 0) {
            $this->info('✅ All PAID invoices with Suspenseutilization records already have settlement_date set!');
            return Command::SUCCESS;
        }

        $this->info("📊 Found {$totalInvoices} PAID invoices with Suspenseutilization records but no settlement_date");

        $updatedCount = 0;
        $skippedCount = 0;
        $errorCount = 0;
        $alreadyUpdatedCount = 0;
        $commitCounter = 0;
        $progressBar = $this->output->createProgressBar($totalInvoices);

        try {
            // Process invoices in chunks using Eloquent - NO TRANSACTIONS
            $invoicesQuery->chunk($chunkSize, function ($invoices) use (&$updatedCount, &$skippedCount, &$errorCount, &$alreadyUpdatedCount, &$commitCounter, $isDryRun, $isDebug, $progressBar, $commitChunkSize) {
                
                foreach ($invoices as $invoice) {
                    try {
                        // Refresh the model to get the latest data from database
                        $invoice->refresh();
                        
                        // Double-check if settlement_date is actually NULL or empty
                        if ($invoice->settlement_date && $invoice->settlement_date !== '0000-00-00 00:00:00') {
                            $alreadyUpdatedCount++;
                            if ($isDebug) {
                                $this->line("DEBUG: PAID Invoice #{$invoice->id} - Already has settlement_date: {$invoice->settlement_date}, skipping");
                            }
                            $progressBar->advance();
                            continue;
                        }

                        // Get the latest created_at date from related Suspenseutilization records
                        $latestSuspenseUtilization = $invoice->receipts()
                            ->orderBy('created_at', 'desc')
                            ->first();
                        
                        if ($latestSuspenseUtilization) {
                            $settlementDate = $latestSuspenseUtilization->created_at;

                            if ($isDebug) {
                                $this->line("DEBUG: PAID Invoice #{$invoice->id} - Found Suspenseutilization with date: {$settlementDate}");
                                $this->line("DEBUG: Current settlement_date: " . ($invoice->settlement_date ?? 'NULL'));
                            }

                            if (!$isDryRun) {
                                // Direct update without transactions
                                $result = $invoice->update(['settlement_date' => $settlementDate]);
                                
                                if ($result) {
                                    $updatedCount++;
                                    $commitCounter++;
                                    
                                    if ($isDebug) {
                                        $this->line("DEBUG: PAID Invoice #{$invoice->id} - Successfully updated");
                                    }
                                    
                                    // Force commit every N updates
                                    if ($commitCounter >= $commitChunkSize) {
                                        DB::commit();
                                        $commitCounter = 0;
                                        
                                        if ($isDebug) {
                                            $this->line("DEBUG: Forced commit after {$commitChunkSize} updates");
                                        }
                                    }
                                } else {
                                    $errorCount++;
                                    if ($isDebug) {
                                        $this->line("DEBUG: PAID Invoice #{$invoice->id} - Update failed");
                                    }
                                }
                            } else {
                                $updatedCount++;
                                $this->line("Would update PAID Invoice #{$invoice->id} ({$invoice->invoicenumber}) with settlement_date: {$settlementDate}");
                            }
                        } else {
                            $skippedCount++;
                            if ($isDebug) {
                                $this->line("DEBUG: PAID Invoice #{$invoice->id} - No Suspenseutilization records found");
                            }
                        }
                        
                    } catch (\Exception $e) {
                        $errorCount++;
                        if ($isDebug) {
                            $this->line("DEBUG: PAID Invoice #{$invoice->id} - Error: " . $e->getMessage());
                        }
                    }
                    
                    $progressBar->advance();
                }
                
                // Clear memory after each chunk
                unset($invoices);
            });

            $progressBar->finish();
            $this->newLine(2);

            // Now check for PAID invoices without Suspenseutilization records
            $this->info('🔍 Checking for PAID invoices without Suspenseutilization records...');
            $paidWithoutSuspense = Invoice::where('status', 'PAID')
                ->whereDoesntHave('receipts')
                ->count();

            if ($isDryRun) {
                $this->info("✅ DRY RUN COMPLETE");
                $this->info("📈 Would update: {$updatedCount} PAID invoices");
                $this->warn("💡 Run without --dry-run to apply changes");
            } else {
                $this->info("✅ SETTLEMENT DATE UPDATE COMPLETE");
                $this->info("📈 Updated: {$updatedCount} PAID invoices");
                $this->info("⏭️  Skipped: {$skippedCount} PAID invoices (no Suspenseutilization records)");
                $this->info("🔄 Already updated: {$alreadyUpdatedCount} PAID invoices (had settlement_date)");
                $this->info("❌ Errors: {$errorCount} PAID invoices");
            }

            // Report on PAID invoices without Suspenseutilization records
            if ($paidWithoutSuspense > 0) {
                $this->warn("⚠️  FOUND {$paidWithoutSuspense} PAID INVOICES WITHOUT SUSPENSEUTILIZATION RECORDS");
                $this->info("💡 These invoices have status 'PAID' but no corresponding payment records");
                $this->info("🔍 This might indicate data inconsistency issues");
            } else {
                $this->info("✅ All PAID invoices have corresponding Suspenseutilization records");
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("❌ Error occurred: " . $e->getMessage());
            $this->info("📈 Successfully updated: {$updatedCount} PAID invoices before error");
            $this->info("⏭️  Skipped: {$skippedCount} PAID invoices");
            $this->info("🔄 Already updated: {$alreadyUpdatedCount} PAID invoices");
            $this->info("❌ Errors: {$errorCount} PAID invoices");
            return Command::FAILURE;
        }
    }
}
