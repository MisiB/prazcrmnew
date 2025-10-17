<?php

namespace App\Console\Commands;

use App\Interfaces\repositories\iuserInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class setupIssuelogapi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:setupissuelogapi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command setup the Admin, Entity and Bidder related tokens';
    protected $userrepo;

    public function __construct(iuserInterface $userrepo)
    {
        parent::__construct();    
        $this->userrepo=$userrepo;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        /**
         * create log
         * read log
         * update log -> for comments
         * recall log
         * close-log
         */
        /**@var User $user */
        $user=$this->userrepo->getuserbyemail("samanikaa@praz.org.zw");

        $admintoken=$user->createToken('admin-token',['admin.access','issue.create','issue.read', 'issue.update', 'issue.recall', 'issue.close']);
        $entitytoken=$user->createToken('entity-token',['entity.access','issue.create','issue.read', 'issue.update', 'issue.recall']);
        $biddertoken=$user->createToken('bidder-token',['bidder.access','issue.create','issue.read', 'issue.update', 'issue.recall']);
        
        $adminplaintext=$admintoken->plainTextToken;
        $entityplaintext=$entitytoken->plainTextToken;
        $bidderplaintext=$biddertoken->plainTextToken;

        $this->info("Admin token: {$adminplaintext}");
        $this->info("Entity token: {$entityplaintext}");
        $this->info("Bidder token: {$bidderplaintext}");
    }
}
