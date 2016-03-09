<?php
class User
{
    public $data = [
        ['id' => 1, 'pbx_id' => '141', 'amo_id' => '557817', 'name' => 'Илья'],
        ['id' => 2, 'pbx_id' => '201', 'amo_id' => '668394', 'name' => 'Яна'],
        ['id' => 3, 'pbx_id' => '203', 'amo_id' => '708804', 'name' => 'Ильфат'],
        ['id' => 4, 'pbx_id' => '206', 'amo_id' => '643530', 'name' => 'Наиль'],
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

    public function getByAmo()
    {
        $res = [];
        foreach ($this->data as $user) {
            $res[$user['amo_id']] = $user;
        }
        return $res;
    }
}
