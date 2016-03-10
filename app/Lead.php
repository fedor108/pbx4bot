<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/User.php';
require_once __DIR__ . '/../vendor/autoload.php';

class Lead
{
    public $api;
    public $data;

    public function __construct()
    {
        $this->api = new AmoRestApi(AMO_DOMAIN, AMO_USER, AMO_KEY);
        $this->data = [];
    }

    public function getCreatedPerUser($params = [])
    {
        $this->update();

        $created = [];
        if (! empty($this->data)) {

            $user = new User;
            $users_by_amo = $user->getByAmo();
            foreach ($users_by_amo as $item) {
                $created[$item['id']] = [
                    'user' => $item,
                    'count' => 0,
                ];
            }

            if (empty($params['create_from'])) {
                $params['create_from'] = strtotime(date('Y-m-d'));
            }

            if (empty($params['create_to'])) {
                $params['create_to'] = time();
            }

            foreach ($this->data as $item) {
                $amo_id = $item['created_user_id'];
                if (! array_key_exists($item['created_user_id'], $users_by_amo)) {
                    continue;
                }

                $id = $users_by_amo[$amo_id]['id'];
                if (($item['date_create'] >= $params['create_from'])
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
        $res = $this->api->getLeadsList(
            $limit_rows = 500,
            $limit_offset = 0,
            $ids = null,
            $query = null,
            $responsible = null,
            $status = null,
            $dateModified = new DateTime(date('Y-m-d'))
        );
        if (!empty($res['leads'])) {
            $this->data = $res['leads'];
        }
    }
}
