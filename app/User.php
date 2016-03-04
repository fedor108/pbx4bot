<?php
class User
{
    public $data = [
        ['pbx_id' => '141', 'amo_id' => '557817', 'name' => 'Иля'],
        ['pbx_id' => '201', 'amo_id' => '668394', 'name' => 'Яна'],
        ['pbx_id' => '203', 'amo_id' => '708804', 'name' => 'Ильфат'],
        ['pbx_id' => '206', 'amo_id' => '643530', 'name' => 'Наиль'],
    ];

    public function get()
    {
        return $this->data;
    }

    public function getByPbx()
    {
        $res = [];
        foreach ($this->data as $user) {
            $res[$user['pbx_id']] = $user;
        }
        return $res;
    }

    public function getAmoPbx()
    {
        $res = [];
        foreach ($this->data as $user) {
            $res[$user['amo_id']] = $user;
        }
        return $res;
    }
}
