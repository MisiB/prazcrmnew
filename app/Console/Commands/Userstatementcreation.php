<?php

namespace App\Console\Commands;

use App\Interfaces\repositories\ileavestatementInterface;
use App\Interfaces\repositories\ileavetypeInterface;
use App\Interfaces\repositories\iuserInterface;
use App\Models\Leavetype;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class Userstatementcreation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:userstatementcreation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command creates statements daily for any newly registered user';

    /**
     * Execute the console command.
     */
    public function handle(iuserInterface $userrepo,ileavetypeInterface $leavetyperepo, ileavestatementInterface $leavestatementrepo)
    {
        //Get the list of available leavetypes from the database
        $leavetypes=$leavetyperepo->getleavetypes();
        $leavetypeidsMap=$leavetypes->map(function($leavetype){
            return [
                'id'=>$leavetype->id,
                'name'=>$leavetype->name
            ];
        });
        /**
         * For each leavetype loop through all the statements
         */
        collect($leavetypeidsMap)->map(function($leavetypedetail) use (&$userrepo, &$leavestatementrepo) {
            //Create new leave statement records for new users
            $userrepo->getall()->each(function($user) use (&$leavetypedetail, &$leavestatementrepo){
                if($leavetypedetail['name']==='Study' || $leavetypedetail['name']==='Sick' || $leavetypedetail['name']==='Maternity' || $leavetypedetail['name']==='Compassionate')
                {
                  
                    $recordExists=$leavestatementrepo->getleavestatementByUserAndLeaveType($user->id, $leavetypedetail['id']);
                    if(!$recordExists)
                    {
                        //create new leavetament record with days set to the min below the ceiling
                        $leavestatementrepo->createleavestatement([
                            "user_id"=>$user->id,
                            "year"=>Carbon::now()->format('Y'),
                            "month"=>Carbon::now()->format('M'),
                            "leavetype_id"=>$leavetypedetail['id'],
                            "days"=>0
                        ]);
                    }
                }
            
            });
        });
        $this->info('User leave statements created successfully.');
        return 0;
    }
}
