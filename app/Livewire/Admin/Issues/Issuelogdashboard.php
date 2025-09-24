<?php

namespace App\Livewire\Admin\Issues;

use App\Interfaces\services\iissueService;
use App\Models\Issuetype;
use App\Models\Issuetask;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Mary\Traits\Toast;
use Livewire\WithPagination;

class Issuelogdashboard extends Component
{
    use Toast, WithPagination;

    public int $requiredIssuetype = 0;
    public ?string $issueQuery = 'App\Models\Issuelog';
    public string $dashboardTitle = "Weekly";
    public string $selectedTab;
    public ?string $search = "";
    public bool $openFilterDrawer = false;
    public int $perPage = 5;
    public array $sortBy = ['column' => 'TransactionDate', 'direction' => 'asc'];
    public $dateFromSearch;
    public $dateToSearch;
    public array $config1 = ['altFormat' => 'Y-m-d'];
    public string $ePaymentsChartColor = "#022c22";
    public string $bankTransactionsChartColor = "#22c55e";
    public string $chartBorderColor = "#ffffff";
    public string $chartVertexColor = "#f1f5f9";
    public string $pendingIssuesChartType = "bar";
    public string $resolvedIssuesChartType = "bar";
    public array $pendingIssuesChart = [];
    public array $resolvedIssuesChart = [];
    public array $issuesTypeChart = [];
    public bool $isDailyPendingChart = false;
    public bool $isDailySettlementsChart = false;

    protected $issueService;

    public function boot(iissueService $issueService)
    {
        $this->issueService = $issueService;
    }

    public function mount()
    {
        $this->dateFromSearch = Carbon::now()->subDays(7)->toDateString();
        $this->dateToSearch = Carbon::now()->toDateString();
        $this->selectedTab = 'assignee-tab';
        $this->isDailyPendingChart = false;
        $this->isDailySettlementsChart = false;
    }

    public function hydrated()
    {
        if ($this->requiredIssuetype != 0) {
            $this->dashboardTitle = Issuetype::where('id', '=', $this->requiredIssuetype)->first()->name;
        }
        $this->getUniqueAssignees();
        $this->getUniqueRespondents();
    }

    public function issuegroups(): array
    {
        return [
            'Procuring Entity' => 1,
            'Supplier' => 2,
            'General' => 3,
            'PRAZ' => 4
        ];
    }

    public function respondentHeaders(): array
    {
        return [
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'email', 'label' => 'Email'],
            ['key' => 'pending_issues', 'label' => 'Pending Issues'],
            ['key' => 'resolved_issues', 'label' => 'Resolved Issues']
        ];
    }

    public function assigneeHeaders(): array
    {
        return [
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'email', 'label' => 'Email'],
            ['key' => 'pending_issues', 'label' => 'Pending Issues'],
            ['key' => 'resolved_issues', 'label' => 'Resolved Issues'],
            ['key' => 'recorded_issues', 'label' => 'Recorded Issues']
        ];
    }

    public function searchFilterQuery(Builder $query_): Builder
    {
        $query = $query_;
        if ($this->search != "") {
            $searchTerms = ['Name', 'Description', 'Ticket', 'Title', 'Email', 'Regnumber'];

            $query = $query->where(function ($query) use ($searchTerms) {
                foreach ($searchTerms as $searchTerm) {
                    $query->orWhere(
                        $searchTerm,
                        'LIKE',
                        '%' . $this->search . '%'
                    );
                }
            });
        }
        return $query;
    }

    public function dateFilterQuery(Builder $query_): Builder
    {
        $query = $query_;
        if ($this->dateFromSearch != null) {
            $query = $query->where([
                ['created_at', '>=', $this->dateFromSearch],
                ['updated_at', '<=', $this->dateToSearch]
            ]);
        }
        return $query;
    }

    public function clear(): void
    {
        $this->reset();
        $this->mount();
        $this->success('Filters cleared.', position: 'toast-bottom');
    }

    public function prazIssues(): Builder
    {
        $query = null;
        if ($this->requiredIssuetype == 0) {
            $query = \App\Models\Issuelog::where('Issuetype_id', '!=', $this->requiredIssuetype);
        } else {
            $query = \App\Models\Issuelog::where('Issuetype_id', '=', $this->requiredIssuetype);
        }
        $query = $query->where('Issuegroup_id', '=', $this->issuegroups()['PRAZ']);
        $query = $this->searchFilterQuery($query);
        $query = $this->dateFilterQuery($query);
        return $query;
    }

    public function procuringEntityIssues(): Builder
    {
        $query = null;
        if ($this->requiredIssuetype == 0) {
            $query = \App\Models\Issuelog::where('Issuetype_id', '!=', $this->requiredIssuetype);
        } else {
            $query = \App\Models\Issuelog::where('Issuetype_id', '=', $this->requiredIssuetype);
        }
        $query = $query->where('Issuegroup_id', '=', $this->issuegroups()['Procuring Entity']);
        $query = $this->searchFilterQuery($query);
        $query = $this->dateFilterQuery($query);
        return $query;
    }

    public function supplierIssues(): Builder
    {
        $query = null;
        if ($this->requiredIssuetype == 0) {
            $query = \App\Models\Issuelog::where('Issuetype_id', '!=', $this->requiredIssuetype);
        } else {
            $query = \App\Models\Issuelog::where('Issuetype_id', '=', $this->requiredIssuetype);
        }
        $query = $query->where('Issuegroup_id', '=', $this->issuegroups()['Supplier']);
        $query = $this->searchFilterQuery($query);
        $query = $this->dateFilterQuery($query);
        return $query;
    }

    public function generalIssues(): Builder
    {
        $query = null;
        if ($this->requiredIssuetype == 0) {
            $query = \App\Models\Issuelog::where('Issuetype_id', '!=', $this->requiredIssuetype);
        } else {
            $query = \App\Models\Issuelog::where('Issuetype_id', '=', $this->requiredIssuetype);
        }
        $query = $query->where('Issuegroup_id', '=', $this->issuegroups()['General']);
        $query = $this->searchFilterQuery($query);
        $query = $this->dateFilterQuery($query);
        return $query;
    }

    public function respondentAssigneeData()
    {
        $respondentAssigneeDataset = Issuetask::select('user_id', 'assigned_by')->get()->map(function ($ticket) {
            return [
                'ticketRespondentId' => $ticket->user_id,
                'ticketAssigneeId' => $ticket->assigned_by
            ];
        });
        return $respondentAssigneeDataset;
    }

    public function getUniqueRespondents()
    {
        $respondents = $this->respondentAssigneeData()->unique('ticketRespondentId')->values()->map(function ($respondent) {
            $user = User::where('id', '=', $respondent['ticketRespondentId'])->select('name', 'email')->first();
            if ($user) {
                $respondent['name'] = $user->name;
                $respondent['email'] = $user->email;
            }
            $respondent['pending_issues'] = Issuetask::where([
                ['type', '=', 'Issue-log'],
                ['user_id', '=', $respondent['ticketRespondentId']],
                ['status', '=', 'PENDING'],
                ['created_at', '>=', $this->dateFromSearch],
                ['updated_at', '<=', $this->dateToSearch]
            ])->count();
            $respondent['resolved_issues'] = Issuetask::where([
                ['type', '=', 'Issue-log'],
                ['user_id', '=', $respondent['ticketRespondentId']],
                ['status', '=', 'RESOLVED'],
                ['created_at', '>=', $this->dateFromSearch],
                ['updated_at', '<=', $this->dateToSearch]
            ])->count();
            return $respondent;
        })->all();
        return $respondents;
    }

    public function getUniqueAssignees()
    {
        $assignees = $this->respondentAssigneeData()->unique('ticketAssigneeId')->values()->map(function ($assignee) {
            $user = User::where('id', '=', $assignee['ticketAssigneeId'])->select('name', 'email')->first();
            if ($user) {
                $assignee['name'] = $user->name;
                $assignee['email'] = $user->email;
            }

            // Use service layer for getting dashboard data
            $filters = [
                'date_from' => $this->dateFromSearch,
                'date_to' => $this->dateToSearch,
                'search' => $this->search
            ];

            if ($this->requiredIssuetype > 0) {
                $filters['issuetype_id'] = $this->requiredIssuetype;
            }

            $dashboardData = $this->issueService->getDashboardData($filters);
            
            $assignee['pending_issues'] = $dashboardData['pending_issues'];
            $assignee['resolved_issues'] = $dashboardData['resolved_issues'];
            $assignee['recorded_issues'] = $dashboardData['total_issues'];

            return $assignee;
        })->all();
        return $assignees;
    }

    public function setPendingIssuesChartData()
    {
        $prazIssues = null;
        $procuringEntityIssues = null;
        $supplierIssues = null;
        $generalIssues = null;

        $labels = null;
        $label = null;
        $backgroundColor = null;
        $borderColor = null;

        if ($this->isDailyPendingChart) {
            $prazIssues = $this->prazIssues()->where('updated_at', '=', Carbon::now()->toDateString())->where('Status', '=', 'PENDING')->count();
            $procuringEntityIssues = $this->procuringEntityIssues()->where('updated_at', '=', Carbon::now()->toDateString())->where('Status', '=', 'PENDING')->count();
            $supplierIssues = $this->supplierIssues()->where('updated_at', '=', Carbon::now()->toDateString())->where('Status', '=', 'PENDING')->count();
            $generalIssues = $this->generalIssues()->where('updated_at', '=', Carbon::now()->toDateString())->where('Status', '=', 'PENDING')->count();
            $labels = [
                'prazIssues' => 'PRAZ Issues',
                'procuringEntityIssues' => 'Procuring Entity Issues',
                'supplierIssues' => 'Supplier Issues',
                'generalIssues' => 'General Issues'
            ];
            $label = "Today's issues";
            $backgroundColor = [$this->ePaymentsChartColor, $this->bankTransactionsChartColor];
            $borderColor = $this->chartBorderColor;
        } else {
            $prazIssues = $this->prazIssues()->where('Status', '=', 'PENDING')->count();
            $procuringEntityIssues = $this->procuringEntityIssues()->where('Status', '=', 'PENDING')->count();
            $supplierIssues = $this->supplierIssues()->where('Status', '=', 'PENDING')->count();
            $generalIssues = $this->generalIssues()->where('Status', '=', 'PENDING')->count();
            $labels = [
                'prazIssues' => 'PRAZ Issues',
                'procuringEntityIssues' => 'Procuring Entity Issues',
                'supplierIssues' => 'Supplier Issues',
                'generalIssues' => 'General Issues'
            ];
            $label = 'Filtered of issues';
            $backgroundColor = [$this->ePaymentsChartColor, $this->bankTransactionsChartColor];
            $borderColor = $this->chartBorderColor;
        }

        $this->pendingIssuesChart = [
            'type' => $this->pendingIssuesChartType,
            'data' => [
                'labels' => [
                    $labels['prazIssues'],
                    $labels['procuringEntityIssues'],
                    $labels['supplierIssues'],
                    $labels['generalIssues'],
                ],
                'datasets' => [
                    [
                        'label' => $label,
                        'data' => [$prazIssues, $procuringEntityIssues, $supplierIssues, $generalIssues],
                        'backgroundColor' => $backgroundColor,
                        'borderColor' => $borderColor,
                    ]
                ]
            ]
        ];
    }

    public function setResolvedIssuesChartData()
    {
        // Similar implementation for resolved issues chart
        $this->resolvedIssuesChart = [
            'type' => $this->resolvedIssuesChartType,
            'data' => [
                'labels' => ['Resolved Issues'],
                'datasets' => [
                    [
                        'label' => 'Resolved Issues',
                        'data' => [0], // Placeholder
                        'backgroundColor' => [$this->ePaymentsChartColor],
                        'borderColor' => $this->chartBorderColor,
                    ]
                ]
            ]
        ];
    }

    public function setIssueTypesChart()
    {
        $labels = $this->getIssuetypes()->pluck('name')->toArray();
        $issueCount = [];

        $indexCount = 1;
        foreach ($labels as $label) {
            array_push($issueCount, \App\Models\Issuelog::where([
                ['created_at', '>=', $this->dateFromSearch],
                ['created_at', '<=', $this->dateToSearch],
                ['Issuetype_id', '=', $indexCount],
            ])->count());
            $indexCount++;
        }

        $this->issuesTypeChart = [
            'type' => 'bar',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => '# of Issues',
                        'data' => $issueCount,
                        'backgroundColor' => $this->ePaymentsChartColor,
                        'borderColor' => $this->bankTransactionsChartColor,
                    ]
                ]
            ]
        ];
    }

    public function getIssuetypes()
    {
        return $this->issueService->getAllIssueTypes();
    }

    public function with(): array
    {
        return [];
    }

    public function render()
    {
        $respondentHeaders = $this->respondentHeaders();
        $respondents = $this->getUniqueRespondents();
        $assigneeHeaders = $this->assigneeHeaders();
        $assignees = $this->getUniqueAssignees();
        $sortBy = $this->sortBy;
        $perPage = $this->perPage;
        $issuetypes = $this->getIssuetypes();
        $prazIssues = $this->prazIssues();
        $procuringEntityIssues = $this->procuringEntityIssues();
        $supplierIssues = $this->supplierIssues();
        $generalIssues = $this->generalIssues();
        $requiredIssuetype = $this->requiredIssuetype;

        $this->setPendingIssuesChartData();
        $this->setResolvedIssuesChartData();
        $this->setIssueTypesChart();

        return view('livewire.admin.issues.issuelogdashboard', compact(
            'respondentHeaders',
            'respondents',
            'assigneeHeaders',
            'assignees',
            'sortBy',
            'perPage',
            'issuetypes',
            'prazIssues',
            'procuringEntityIssues',
            'supplierIssues',
            'generalIssues',
            'requiredIssuetype',
        ));
    }
}
