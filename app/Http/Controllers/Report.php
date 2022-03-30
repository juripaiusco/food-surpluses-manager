<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class Report extends Controller
{
    var $path_report_csv = 'report/csv/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function get_reports()
    {
        $orders = \App\Model\Order::where('date', 'LIKE', date('Y-m-d') . '%')
            ->get();
        $reports = array();

        // Prendo la lista degli ordini
        foreach ($orders as $order) {

            $products_obj = json_decode($order->json_products);
            $customer_obj = json_decode($order->json_customer);

            // Ogni ordine ha una lista di prodotti che ciclo
            foreach ($products_obj as $product) {

                // Recupero soltanto i prodotti FEAD
                if ($product->type == 'fead') {

                    if (!isset($n_family_total[$customer_obj->cod]))
                        $n_family_total[$product->cod][$customer_obj->cod] = 0;

                    $reports[$product->cod]['product'] = $product;

                    // Se voglio contare tutte le volte che ogni cliente ha acquistato il prodotto
                    /*$reports[$product->cod]['customers'][] = $customer_obj;
                    $n_family_total[$product->cod][] = $customer_obj->family_number;*/

                    // Se voglio contare da quale singola famiglia è stato acquistato il prodotto
                    $reports[$product->cod]['customers'][$customer_obj->cod] = $customer_obj;
                    $n_family_total[$product->cod][$customer_obj->cod] = $customer_obj->family_number;

                    $reports[$product->cod]['customers_count'] = array(
                        'n_family' => count($reports[$product->cod]['customers']),
                        'n_family_total' => array_sum($n_family_total[$product->cod])
                    );

                }
            }
        }

        return $reports;
    }

    public function index()
    {
        $reports = $this->get_reports();

        return view('report.list', [
            'reports' => $reports
        ]);
    }

    public function csvMake($data, $out_name)
    {
        if (count($data) > 0) {

            // Creo la directory
            $path_reportCSV_send = Storage::disk('public')->makeDirectory($this->path_report_csv . 'send');
            $path_reportCSV_queue = Storage::disk('public')->makeDirectory($this->path_report_csv . 'queue');

            if ($path_reportCSV_queue) {

                $csv_content = 'Prodotto;Famiglie n.;Componenti n. tot.' . "\n";

                foreach ($data as $d) {
                    $csv_content .= $d['product']->cod . ' - ' . $d['product']->name . ';';
                    $csv_content .= $d['customers_count']['n_family'] . ';';
                    $csv_content .= $d['customers_count']['n_family_total'];
                    $csv_content .= "\n";
                }

                $path_report_csv = Storage::disk('public')->put(
                    $this->path_report_csv . 'queue/' . $out_name,
                    $csv_content
                );
            }
        }
    }

    public function mailSend()
    {
        $host = current(explode('.', \request()->getHttpHost()));

        // Creo i file CSV
        $this->csvMake($this->get_reports(), $host . '_' . date('Ymd') . '.csv');
        $files = Storage::disk('public')->files($this->path_report_csv . 'queue/');

        // Verifico se esistono file da inviare
        if (count($files) > 0) {

            // Invio email con i file CSV come allegato
            Mail::to(env('MAIL_TO'))
                ->send(new \App\Mail\Report(array(
                    'host' => $host,
                    'attach_path' => $this->path_report_csv . 'queue/'
                )));

            // Se la mail è andata a buon fine sposto i file CSV nella directory send
            if (count(Mail::failures()) == 0) {

                $files = Storage::disk('public')->files($this->path_report_csv . 'queue/');

                foreach ($files as $file) {

                    // Se il file esiste viene eliminato
                    Storage::disk('public')->delete($this->path_report_csv . 'send/' . basename($file));

                    Storage::disk('public')->move(
                        $file,
                        $this->path_report_csv . 'send/' . basename($file)
                    );
                }
            }
        }
    }

}
