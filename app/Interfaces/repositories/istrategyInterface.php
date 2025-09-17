<?php

namespace App\Interfaces\repositories;

interface istrategyInterface
{
    public function getstrategies();
    public function getstrategy($id);
    public function createstrategy(array $data);
    public function updatestrategy($id,array $data);
    public function deletestrategy($id);
    public function approvestrategy($id);
    public function unapprovestrategy($id);
    public function copystrategy($id,$data);
    public function addstrategycomments($id,array $data);
    public function updatestrategycomments($id,array $data);
    public function deletestrategycomments($id);

   public function getstrategybyuuid($uuid);
   public function getprogramme($id);
   public function getprogrammebyuuid($uuid,$id);
   public function createstrategyprogramme(array $data);
   public function updatestrategyprogramme($id,array $data);
   public function deletestrategyprogramme($id);
   public function getprogrammeoutcome($id);
   public function getprogrammeoutcomebyuuid($programme_id,$outcome_id);
   public function createstrategyprogrammeoutcome(array $data);
   public function updatestrategyprogrammeoutcome($id,array $data);
   public function deletestrategyprogrammeoutcome($id);
   public function getprogrammeoutcomeindicator($id);
   public function createprogrammeoutcomeindicator(array $data);
   public function updateprogrammeoutcomeindicator($id,array $data);
   public function deleteprogrammeoutcomeindicator($id);
   public function createsubprogramme(array $data);
   public function getsubprogramme($id);
   public function updatesubprogramme($id,array $data);
   public function deletesubprogramme($id);

}
