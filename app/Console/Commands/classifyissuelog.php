<?php

namespace App\Console\Commands;

use App\Models\Issuetask;
use Illuminate\Console\Command;
//use LLoadout\MicrosoftGraph\Facades\Graph; // or whatever alias the package uses
use App\Models\Ticket;
use App\Services\TicketClassifier;
use Illuminate\Support\Facades\Log ;
use LLoadout\Microsoftgraph\MailManager\MicrosoftGraphMailManager;
use Prism\Prism\Prism;

class classifyissuelog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:fetch-outlook';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch unread Outlook / Office365 emails and log/classify them';

    
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fetching unread Outlook emails...');

        // Using Graph wrapper to get messages
        $messages = MicrosoftGraphMailManager::getMailMessagesFromFolder('Inbox', isRead: false, limit: 10);

        foreach ($messages as $message) {
            $id = $message['id'] ?? null;
            $subject = $message['subject'] ?? '(No Subject)';
            // 'body' might be content + contentType
            $bodyObj = $message['body'] ?? null;
            // either HTML or text
            $body = '';
            if ($bodyObj) {
                if ($bodyObj['contentType'] === 'text') {
                    $body = $bodyObj['content'];
                } else {
                    // optionally strip HTML tags
                    $body = strip_tags($bodyObj['content']);
                }
            }

            // Log extracting
            Log::info('Outlook Email as Ticket', [
                'message_id' => $id,
                'subject' => $subject,
                'description' => $body,
            ]);

            // Persist a ticket
            $ticket = Issuetask::create([
                'title' => $subject,
                'description' => $body,
                'category' => null,
                'priority' => null,
            ]);

            // Classify
            try 
            {
                $classification = $this->classifyTicket($subject, $body);
                $ticket->update([
                    'category' => $classification['category'] ?? $ticket->category,
                    'priority' => $classification['priority'] ?? $ticket->priority,
                ]);
                $this->info("Ticket {$ticket->id} classified as {$ticket->category} / {$ticket->priority}");
            } catch (\Exception $e) {
                Log::error("Classification failed for Outlook message {$id}: " . $e->getMessage());
            }

            // Mark message as read so it's not processed again
            MicrosoftGraph::updateMessage($id, ['isRead' => true]);
        }

        $this->info('Done processing Outlook emails.');
        return 0;
    }

    protected function classifyTicket(string $title, string $description): array
    {
        // Use Prism to call the LLM -- e.g. zero-shot classification or few‐shot
        $prompt = "You are a ticket classifier. Given a ticket title and description, classify into category and priority. Categories could be: Technical, Billing, Feature Request, General Support. Priority levels: Low, Medium, High.\n\n"
                . "Title: {$title}\n"
                . "Description: {$description}\n\n"
                . "Provide output in JSON with keys: category, priority.";

        // Open AI
        $prism = Prism::text()
            ->using('openai', 'gpt-4o')
            ->withSystemPrompt(view('prompts.nyx'))
            ->withPrompt($prompt);

        $response = $prism();

        // parse response
        // doing naïve JSON parsing; production code should have fallback, validation
        $decoded = json_decode($response->text(), true);

        if (! is_array($decoded) || !isset($decoded['category'])) {
            // fallback defaults
            return [
                'category' => 'General Support',
                'priority' => 'Medium',
            ];
        }

        return [
            'category' => $decoded['category'],
            'priority' => $decoded['priority'] ?? 'Medium',
        ];
    }
}
