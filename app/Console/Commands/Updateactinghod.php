<?php

namespace App\Console\Commands;


use App\Interfaces\services\ileaverequestService;
use App\Notifications\LeaverequestSubmitted;
use Carbon\Carbon;
use Illuminate\Console\Command;

class Updateactinghod extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:updateactinghod';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command updates Acting HOD role incase the actual HOD returns from leave, hence stopping the notifications to the Acting HOD';
    protected $hodrole='Acting HOD';
    /**
     * Execute the console command. For the pending requests
     */
    public function handle(ileaverequestService $leaverequestService)
    {
        $leaverequestService->getleaverequestbystatus('A')->each(function($requestrecord) use(&$leaverequestService, &$hodrole){
            if($requestrecord->actinghod_id!=null)
            {
                $currentdate=Carbon::now();//$currentdate=Carbon::parse("2025-10-06");
                $returndate=Carbon::parse($requestrecord->returndate)->startOfDay();
                if( $currentdate->equalTo($returndate)|| $currentdate->greaterThan($returndate) ){
                    $acting_hod=$leaverequestService->getuser($requestrecord->actinghod_id);
                    $acting_hod->removeRole($this->hodrole);//'Acting HOD' Role
                    
                    $hodapprovalrecord=$leaverequestService->getleaverequestapproval($requestrecord->leaverequestuuid);
                    //Notify actual HOD Of the pending leave tasks left behind
                    $leaverequestService->getleaverequestbystatus('P')->each(function($userrequestrecord) use(&$leaverequestService,&$hodapprovalrecord){

                        $userapprovalrecord=$leaverequestService->getleaverequestapproval($userrequestrecord->leaverequestuuid);
                        if($userapprovalrecord->user_id===$hodapprovalrecord->user_id)
                        {
                            $hoduser=$leaverequestService->getuser($userapprovalrecord->user_id);
                            $hoduser->notify(new LeaverequestSubmitted($leaverequestService, $userapprovalrecord->leaverequest_uuid)); 
                        }
                    });
                    $this->info("Acting HOD Role Updated Successfully");
                }
            }
        });
}
}
