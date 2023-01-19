<?php
namespace App\Repo\Interfaces;

interface IChildBirth{
    // public function saveMotherAndBabyData($req,$motherData,$babyData);
    public function saveChildAndMotherData($request);
    public function getBirthHistory($request);
    public function updateChildData($request,$id);
    public function detailChildData($id);
    public function destroyChildData($id);
    public function getReportList();
    public function getAnnualReport($year);
    public function getMonthlyReport($year,$month);
}