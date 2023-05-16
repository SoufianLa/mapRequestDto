<?php

namespace App\Helper;

use Symfony\Component\Validator\ConstraintViolationListInterface;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;

class Helper
{
    public static function isEmptyParams(...$params): bool
    {
        foreach ($params as $param) {
            if (!isset($param) || empty($param)) {
                return true;
            }
        }
        return false;
    }

    public static function render(string $message, $body = null): array
    {
        return ['message' => $message, 'body' => isset($body) ? $body : new \ArrayObject()];
    }

    public static function formatViolationMessage(ConstraintViolationListInterface $list)
    {
        if (count($list) > 0) {
            $violation = $list->get(0);
            return ucfirst($violation->getPropertyPath()) . " : " . $violation->getMessage();
        }
        return false;
    }

    /**
     * @return string
     */
    public static function randomPassword(): string
    {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $pass = [];
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass);
    }

    public static function formatPaginationData(SlidingPagination $paginator): array
    {
        return [
            "data" => $paginator->getItems(),
            "meta" => [
                "limit" => $paginator->getItemNumberPerPage(),
                "total_items" => $paginator->getTotalItemCount(),
                "page" => $paginator->getCurrentPageNumber(),
                "total_pages" => ceil($paginator->getTotalItemCount() / $paginator->getItemNumberPerPage())
            ]
        ];

    }

    public static function curl_request(array $params)
    {
        /**
         * @var $url
         * @var $headers
         * @var $fields
         */
        extract($params);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     * @param string $jid
     * @return string
     */
    public static function rawJid(string $jid): string
    {
        $toRemove = "@" . $_SERVER["OF_HOST"];
        return str_replace($toRemove, "",$jid);
    }

    /**
     * @param string $title
     * @param string $message
     * @param null $data
     * @return array
     */
    public static function formatContextPushMessage(string $title, string $message, $data = null): array
    {
        return [
            "pTitle" => $title,
            "pMessage" => $message,
            "pData" => $data
        ];
    }

    /**
     * @param $startDate
     * @param $endDate
     * @return array
     */
    public static function getBetweenDates($startDate, $endDate)
    {
        $rangArray = [];
        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);
        for ($currentDate = $startDate; $currentDate <= $endDate; $currentDate += (86400))
        {
            $date = date('Y-m-d', $currentDate);
            $rangArray[] = $date;
        }
        return $rangArray;
    }

    /**
     * @param $date
     * @return bool
     */
    public static function isWeekend($date) {
        return (date('N', strtotime($date)) >= 6);
    }
    public static function formatMetaDataOrdersAndQuotations($rawMetaDataOrders, $rawMetaDataQuotations) : array
    {
        $result = [
            "takfoun_orders" =>
                [
                    "new"=> 0,
                    "accepted"=> 0
                ],
            "normal_orders"=>
                [
                    "new"=> 0,
                    "accepted"=> 0
                ],
            "quotations"=>
                [
                    "open"=> 0,
                    "answered"=> 0
            ],
            "total_orders" => [
                "new"=> 0,
                "accepted"=> 0
            ]
        ];
        foreach ($rawMetaDataOrders as $d)
        {
            if($d["type"] == "takfoun")
            {
                switch ($d["state"]){
                    case "new":
                        $result["takfoun_orders"]["new"] = $d["total"];
                        break;
                    case "accepted":
                        $result["takfoun_orders"]["accepted"] = $d["total"];;
                        break;
                    default:
                        break;
                }
            }else
            {
                switch ($d["state"]){
                    case "new":
                        $result["normal_orders"]["new"] = $d["total"];
                        break;
                    case "accepted":
                        $result["normal_orders"]["accepted"] = $d["total"];;
                        break;
                    default:
                        break;
                }
            }
        }
        foreach ($rawMetaDataQuotations as $qd)
        {
            switch ($qd["state"]){
                case "open":
                    $result["quotations"]["open"] = $qd["total"];
                    break;
                case "answered":
                    $result["quotations"]["answered"] = $qd["total"];;
                    break;
                default:
                    break;
            }
        }
        $result["total_orders"]["new"] = $result["takfoun_orders"]["new"] + $result["normal_orders"]["new"];
        $result["total_orders"]["accepted"] = $result["takfoun_orders"]["accepted"] + $result["normal_orders"]["accepted"];
        return $result;
    }

}
