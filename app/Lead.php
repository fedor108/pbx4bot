<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/User.php';
require_once __DIR__ . '/../vendor/autoload.php';

class Lead
{
    public $api;
    public $data;
    public $result;
    public $leads;

    public function __construct()
    {
        $this->api = new AmoRestApi(AMO_DOMAIN, AMO_USER, AMO_KEY);
        $this->file = __DIR__ . '/../data/leads';
        $this->leads = [];
    }

    public function getCreatedPerUser($params = [])
    {
        $this->update();

        $created = [];
        if (! empty($this->leads)) {

            $user = new User;
            $users_by_amo = $user->getByAmo();

            if (empty($params['create_from'])) {
                $params['create_from'] = strtotime(date('Y-m-d'));
            }

            if (empty($params['create_to'])) {
                $params['create_to'] = time();
            }

            foreach ($this->leads as $item) {
                $amo_id = $item['created_user_id'];
                if (! array_key_exists($item['created_user_id'], $users_by_amo)) {
                    continue;
                }
                $id = $users_by_amo[$amo_id]['id'];
                if (empty($created[$id])) {
                    $created[$id] = [
                        // 'items' => [],
                        'user' => $users_by_amo[$amo_id],
                        'count' => 0,
                    ];
                } elseif (($item['date_create'] >= $params['create_from'])
                    && ($item['date_create'] <= $params['create_to'])
                ) {
                    // $created[$id]['items'][] = $item;
                    $created[$id]['count']++;
                }
            }
        }

        return $created;
    }

    public function update()
    {
        $data = $this->api->getLeadsList(
            $limit_rows,
            $limit_offset,
            $ids = null,
            $query = null,
            $responsible = null,
            $status = null
            // $dateModified = new DateTime(date('Y-m-d'))
        );

        file_put_contents(__DIR__ . '/../tmp/leads', print_r($data, true));
        $this->leads = $data['leads'];

        $this->saveFile();
    }

    private function getFile()
    {
        $limit_offset = 0;
        if (file_exists($this->file)) {
            $this->leads = json_decode(file_get_contents($this->file), true);
            $limit_offset = count($this->leads);
        }
        return $limit_offset;
    }

    private function getAmo($limit_offset = 0, $limit_rows = 500)
    {

    }

    private function saveFile()
    {
        $path = pathinfo($this->file);
        if (! file_exists($path['dirname'])) {
            mkdir($path['dirname'], 0775, true);
        }
        file_put_contents($this->file, json_encode($this->leads));
    }
}
