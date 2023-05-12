<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Cron extends Controller
{
    public function reportSend()
    {
        $report = new Report();
        $report->mailSend();
    }

    public function customerRenew()
    {
        $customer = new Customer();
        $customer->points_renew();
    }

    public function db2Exel()
    {
        $setting = new Setting();
        $setting->db2Excel();
    }
}
