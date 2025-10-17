<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\CreateTicketRequest;
use App\Http\Resources\V1\CustomerCollection;
use App\Http\Resources\V1\IssueCollection;
use App\Http\Resources\V1\IssuegroupCollection;
use App\Http\Resources\V1\IssuegroupResource;
use App\Http\Resources\V1\IssueResource;
use App\Http\Resources\V1\IssuetypeCollection;
use App\Interfaces\services\iissuefilterService;
use App\Interfaces\services\iissueService;
use App\Interfaces\services\iservicecustomerInterface;
use App\Models\Issuelog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class issueController extends Controller
{     
    protected $issueService;
    protected $customerService;
    protected $issuefilterService;
    public function __construct(iissueService $issueService, iservicecustomerInterface $customerService, iissuefilterService $issuefilterService)
    {
        $this->issueService = $issueService;
        $this->customerService=$customerService;
        $this->issuefilterService=$issuefilterService;
    }

    public function index()
    {
        /**@var User $user */
        $user=Auth::user();
        if(!$user->tokenCan('admin.access'))
        {
            $response=["status"=>"warning", "message"=>"access restricted"];
            return response()->json($response,403);
        }
        return new IssueCollection( $this->issueService->getallissuelogs() );
    }
    public function show($ticketnumber)
    {        
        /**@var User $user */
        $user=Auth::user();
        if(!$user->tokenCan('admin.access'))
        {
            abort(403);
        }
        return new IssueResource( $this->issueService->getissuelogbyticket($ticketnumber) );
    }
    public function update()
    {
        
    }
    public function saverecord(Request $request, $userid)
    {

    }    
    public function getentityissuegroups()
    {
        /**@var User $user */
        $user=Auth::user();
        if($user->tokenCan('admin.access')||$user->tokenCan('entity.access'))
        {
            return new IssuegroupCollection( $this->issueService->getentityissuegroups() );
        }
        $response=["status"=>"warning", "message"=>"access restricted"];
        return response()->json($response,403);
      
    }
    public function getbidderissuegroups()
    {
        /**@var User $user */
        $user=Auth::user();
        if($user->tokenCan('admin.access')||$user->tokenCan('bidder.access'))
        {
            return new IssuegroupCollection( $this->issueService->getbidderissuegroups() );
        }
        $response=["status"=>"warning", "message"=>"access restricted"];
        return response()->json($response,403);
    }
    public function getentityissuetypes()
    {
        /**@var User $user */
        $user=Auth::user();
        if($user->tokenCan('admin.access')||$user->tokenCan('entity.access'))
        {
            $entityissuetypes=['app','tender','registration', 'payment', 'invoice', 'refund', 'general'];
            $issuetypes=$this->issueService->getallissuetypes()->filter(function($issuetype) use ($entityissuetypes){
                return in_array(strtolower($issuetype->name),$entityissuetypes);
            });
            return new IssuetypeCollection( $issuetypes );
        }
        $response=["status"=>"warning", "message"=>"access restricted"];
        return response()->json($response,403);
    }
    public function getbidderissuetypes()
    {
        /**@var User $user */
        $user=Auth::user();
        if($user->tokenCan('admin.access')||$user->tokenCan('bidder.access'))
        {
            $bidderissuetypes=['tender','registration', 'payment', 'invoice', 'document verification', 'refund', 'general'];
            $issuetypes=$this->issueService->getallissuetypes()->filter(function($issuetype) use ($bidderissuetypes){
                return in_array(strtolower($issuetype->name),$bidderissuetypes);
            });
            return new IssuetypeCollection( $issuetypes );
        }        
        $response=["status"=>"warning", "message"=>"access restricted"];
        return response()->json($response,403);
    }
    public function getcustomers()
    {
        return new CustomerCollection($this->customerService->getall());
    }
    public function getissuesbyorganization(Request $request)
    {
        
        /**@var User $user **/
        $user=Auth::user();
        if($user->tokenCan('bidder.access')||$user->tokenCan('entity.access'))
        {

            $query=$this->issuefilterService->transform($request);
            if(count($query)==0)
            {
                $response=["status"=>"warning", "message"=>"no logs found"];
                return response()->json($response,400);
            }
            $data=null;
            foreach($query as $key=>$queryparam)
            {
                if($key==0)
                {
                    $data=$this->issueService->getallissuelogs()->where($queryparam[0],$queryparam[1],$queryparam[2]);
                    continue;
                }
                $data=$data->where($queryparam[0],$queryparam[1],$queryparam[2]);
            }
            return new IssueCollection( $data);
        }
        $response=["status"=>"warning", "message"=>"access restricted"];
        return response()->json($response,403);

    }   
    public function createticket(CreateTicketRequest $request)
    {
        $reponse=$this->issueService->createissuelog($request->all());
        return response()->json($reponse);
    }

    public function gettoken(Request $request)
    {
        $response=$this->issueService->gettoken($request->regnumber, $request->userlevel);
        return response()->json($response);
    }

}
