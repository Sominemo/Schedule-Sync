<?php

class Report {
    private $report_table = "api_report";
    private $id = false;
    private $data = false;

    public function __construct($data = []) {
        global $pdo, $global_report_data, $the_return_stream;

        if(DB_CONNECTION_SUCCESS !== true) return;

        $global_report_data['result'] = (count($the_return_stream) > 0 ? $the_return_stream : $data);
        $global_report_data['time'] = microtime(true) - $global_report_data['time'];
        $global_report_data['user_id'] = (Auth::User() ? Auth::User()->get()['id'] : 0);

        $this->data = $global_report_data;
        $v = db::values($global_report_data);

        $r = $pdo->prepare("INSERT into `{$this->report_table}` SET $v");
        $r->execute($global_report_data);
        $m = intval($pdo->lastInsertId());

        if (!$m > 0) {throw new apiException(104); return false;}
        $this->id = $m;

        return true;
    }

    public function getData() {
        return $this->data;
    }

    public function getID() {
        return $this->id;
    }
}