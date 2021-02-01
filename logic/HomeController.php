<?php

namespace App\Http\Controllers;

//Áp dụng cho trang web: https://vnexpress.net/
//Chạy bằng framework Laravel
// Cài thêm extension curl nếu thiếu
// Phiên bản PHP 7. trở lên
// Route::get(/,'HomeController@getDataFromLink');
class HomeController extends Controller
{

    public function getDataFromLink()
    {
        $data = [
            'url' => [],
            'image' => []
        ];
        $domain = "vnexpress.net";
        $contentPage = $this->getContentFromLink('https://vnexpress.net/');
        // Hàm lấy ra list Link dữ liệu thô cần phải lọc thêm
        if ($contentPage) {
            $data['url'] = $this->filterLinkPage($contentPage, $domain);
            $data['image'] = $this->filterLinkImage($contentPage, 's1.vnecdn.net');
        }
        return $data;
    }

    public function getContentFromLink($url)
    {
        $cURL = curl_init($url);
        curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURL, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($cURL, CURLOPT_SSL_VERIFYHOST, false);
        $result = curl_exec($cURL);

        $statusCode = curl_getinfo($cURL, CURLINFO_HTTP_CODE);
        curl_close($cURL);
        if ($statusCode == 200) {
            return $result;
        } else {
            echo 'Thao tác không thành công';
            return null;
        }
    }

    public function filterLinkPage($contentPage, $domain)
    {
        $data = [];
        $regex = '@(https://vnexpress.net+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[.html])?)?)@';


        if (preg_match_all($regex, $contentPage, $listLink)) {
            if (!empty($listLink)) {
                foreach ($listLink as $listLinkSub) {
                    if ($listLinkSub) {
                        foreach ($listLinkSub as $item) {
                            // Lọc ra link có chứa đuôi html mới lấy
                            $checkHtml = strripos($item, ".html");
                            // Lọc ra link có chứa domain /vnexpress.net/
                            $checkDomain = strripos($item, "/" . $domain . "/");
                            if (!empty($item) && $checkHtml > 0 && $checkDomain > 0) {
                                array_push($data, $item);
                            }
                        }
                    }
                }

            }
        }
        if ($data) {
            // Xóa những link bị duplicate
            $data = array_unique($data);
        }
        return $data;
    }

    // Hàm loc image
    public function filterLinkImage($contentPage, $domain)
    {
        $data = [];
        $rg2 = '@(https://s1.vnecdn.net+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[.:png|jpg|gif|svg|jpeg])?)?)@';
        if (preg_match_all($rg2, $contentPage, $listLink)) {
            foreach ($listLink as $listLinkSub) {
                if ($listLinkSub) {
                    foreach ($listLinkSub as $item) {
                        $checkDomain = strripos($item, "/" . $domain . "/");
                        $check = substr($item, -3);
                        if (!empty($item) && $checkDomain > 0 && in_array($check, ['png', 'jpg', 'gif', 'svg', 'jpeg'])) {
                            array_push($data, $item);
                        }
                    }
                }
            }
        }
        return $data;
    }
}
//output
//array:2 [▼
//  "url" => array:48 [▼
//    0 => "https://vnexpress.net/pho-thu-tuong-vu-duc-dam-tra-loi-truc-tuyen-tren-vnexpress-4223153.html"
//    1 => "https://vnexpress.net/tong-bi-thu-nguyen-phu-trong-tai-dac-cu-trung-uong-khoa-xiii-4228934.html"
//    5 => "https://vnexpress.net/tong-bi-thu-nguyen-phu-trong-tai-dac-cu-trung-uong-khoa-xiii-4228934.html#box_comment"
//    6 => "https://vnexpress.net/danh-sach-200-uy-vien-trung-uong-khoa-xiii-4228952.html"
//    8 => "https://vnexpress.net/danh-sach-200-uy-vien-trung-uong-khoa-xiii-4228952.html#box_comment"
//    9 => "https://vnexpress.net/hon-9-800-nguoi-tu-vung-dich-tro-ve-ha-noi-4229031.html"
//    11 => "https://vnexpress.net/hon-9-800-nguoi-tu-vung-dich-tro-ve-ha-noi-4229031.html#box_comment"
//    12 => "https://vnexpress.net/song-kieu-moi-4228953.html"
//    14 => "https://vnexpress.net/tac-gia/jesse-peterson-1050.html"
//    15 => "https://vnexpress.net/song-kieu-moi-4228953.html#box_comment"
//    17 => "https://vnexpress.net/bo-truong-phung-xuan-nha-khong-trung-cu-trung-uong-khoa-moi-4229099.html"
//    19 => "https://vnexpress.net/bo-truong-phung-xuan-nha-khong-trung-cu-trung-uong-khoa-moi-4229099.html#box_comment"
//    20 => "https://vnexpress.net/dai-hoi-xiii-bau-ban-chap-hanh-trung-uong-khoa-moi-4229029.html"
//    21 => "https://vnexpress.net/dai-hoi-xiii-bau-ban-chap-hanh-trung-uong-khoa-moi-4229029.html#box_comment"
//    22 => "https://vnexpress.net/them-28-ca-covid-19-4229030.html"
//    25 => "https://vnexpress.net/them-28-ca-covid-19-4229030.html#box_comment"
//    26 => "https://vnexpress.net/ha-noi-xem-xet-cho-hoc-sinh-nghi-tet-som-4229082.html"
//    29 => "https://vnexpress.net/ha-noi-xem-xet-cho-hoc-sinh-nghi-tet-som-4229082.html#box_comment"
//    30 => "https://vnexpress.net/tp-hcm-xet-nghiem-349-nguoi-den-tu-vung-co-covid-19-4229070.html"
//    32 => "https://vnexpress.net/tp-hcm-xet-nghiem-349-nguoi-den-tu-vung-co-covid-19-4229070.html#box_comment"
//    33 => "https://vnexpress.net/cach-ly-7-y-bac-si-benh-vien-tai-mui-hong-tiep-xuc-benh-nhan-covid-19-4229024.html"
//    35 => "https://vnexpress.net/cach-ly-7-y-bac-si-benh-vien-tai-mui-hong-tiep-xuc-benh-nhan-covid-19-4229024.html#box_comment"
//    36 => "https://vnexpress.net/phe-cong-hoa-kho-thieu-trump-4228411.html"
//    39 => "https://vnexpress.net/phe-cong-hoa-kho-thieu-trump-4228411.html#box_comment"
//    40 => "https://vnexpress.net/benh-nhan-covid-19-o-ha-noi-tiep-xuc-nhieu-nguoi-4229043.html"
//    42 => "https://vnexpress.net/benh-nhan-covid-19-o-ha-noi-tiep-xuc-nhieu-nguoi-4229043.html#box_comment"
//    43 => "https://vnexpress.net/5-ca-nghi-nhiem-gia-lai-phong-toa-hon-100-000-dan-4228940.html"
//    46 => "https://vnexpress.net/5-ca-nghi-nhiem-gia-lai-phong-toa-hon-100-000-dan-4228940.html#box_comment"
//    47 => "https://vnexpress.net/benh-vien-tre-em-hai-phong-hoat-dong-tro-lai-4229066.html"
//    49 => "https://vnexpress.net/benh-vien-tre-em-hai-phong-hoat-dong-tro-lai-4229066.html#box_comment"
//    50 => "https://vnexpress.net/bat-duoc-ca-sau-o-ho-nuoc-vung-tau-4229033.html"
//    53 => "https://vnexpress.net/bat-duoc-ca-sau-o-ho-nuoc-vung-tau-4229033.html#box_comment"
//    54 => "https://vnexpress.net/chong-chui-boi-khi-toi-khong-the-tiep-tuc-nuoi-anh-4228710.html"
//    57 => "https://vnexpress.net/chong-chui-boi-khi-toi-khong-the-tiep-tuc-nuoi-anh-4228710.html#box_comment"
//    58 => "https://vnexpress.net/thanh-pho-chi-linh-thieu-cho-cach-ly-4229006.html"
//    61 => "https://vnexpress.net/thanh-pho-chi-linh-thieu-cho-cach-ly-4229006.html#box_comment"
//    62 => "https://vnexpress.net/tinh-thu-nam-cho-hoc-sinh-nghi-hoc-4228996.html"
//    64 => "https://vnexpress.net/tinh-thu-nam-cho-hoc-sinh-nghi-hoc-4228996.html#box_comment"
//    65 => "https://vnexpress.net/quang-ninh-truy-vet-duoc-hon-23-600-f1-den-f4-4228967.html"
//    68 => "https://vnexpress.net/quang-ninh-truy-vet-duoc-hon-23-600-f1-den-f4-4228967.html#box_comment"
//    69 => "https://vnexpress.net/bo-y-te-tim-nguoi-den-hai-dia-diem-o-thai-binh-4228090.html"
//    71 => "https://vnexpress.net/bo-y-te-tim-nguoi-den-hai-dia-diem-o-thai-binh-4228090.html#box_comment"
//    72 => "https://vnexpress.net/ca-benh-moi-o-van-don-tiep-xuc-nhieu-nguoi-4228840.html"
//    75 => "https://vnexpress.net/ca-benh-moi-o-van-don-tiep-xuc-nhieu-nguoi-4228840.html#box_comment"
//    76 => "https://vnexpress.net/viet-nam-cap-phep-luu-hanh-vaccine-covid-19-dau-tien-4228910.html"
//    79 => "https://vnexpress.net/viet-nam-cap-phep-luu-hanh-vaccine-covid-19-dau-tien-4228910.html#box_comment"
//    80 => "https://vnexpress.net/hon-1-200-nguoi-tp-hcm-ve-tu-hai-duong-quang-ninh-4228932.html"
//    83 => "https://vnexpress.net/hon-1-200-nguoi-tp-hcm-ve-tu-hai-duong-quang-ninh-4228932.html#box_comment"
//  ]
//  "image" => array:22 [▼
//    0 => "https://s1.vnecdn.net/vnexpress/restruct/i/v372/logo_default.jpg"
//    1 => "https://s1.vnecdn.net/vnexpress/restruct/i/v372/logo_default.jpg"
//    2 => "https://s1.vnecdn.net/vnexpress/restruct/i/v372/logos/72x72.png"
//    3 => "https://s1.vnecdn.net/vnexpress/restruct/i/v372/logos/114x114.png"
//    4 => "https://s1.vnecdn.net/vnexpress/restruct/i/v372/logos/57x57.png"
//    5 => "https://s1.vnecdn.net/vnexpress/restruct/i/v372/logos/114x114.png"
//    6 => "https://s1.vnecdn.net/vnexpress/restruct/i/v372/logos/57x57.png"
//    7 => "https://s1.vnecdn.net/vnexpress/restruct/i/v372/logos/114x114.png"
//    8 => "https://s1.vnecdn.net/vnexpress/restruct/i/v372/v2_2019/pc/graphics/parten-tet.png"
//    9 => "https://s1.vnecdn.net/vnexpress/restruct/i/v372/v2_2019/pc/graphics/logo.svg"
//    10 => "https://s1.vnecdn.net/vnexpress/restruct/i/v372/v2_2019/pc/graphics/bg-dhd-pc.svg"
//    11 => "https://s1.vnecdn.net/vnexpress/restruct/i/v372/logo_default.jpg"
//    12 => "https://s1.vnecdn.net/vnexpress/restruct/i/v372/logo_default.jpg"
//    13 => "https://s1.vnecdn.net/vnexpress/restruct/i/v372/logos/72x72.png"
//    14 => "https://s1.vnecdn.net/vnexpress/restruct/i/v372/logos/114x114.png"
//    15 => "https://s1.vnecdn.net/vnexpress/restruct/i/v372/logos/57x57.png"
//    16 => "https://s1.vnecdn.net/vnexpress/restruct/i/v372/logos/114x114.png"
//    17 => "https://s1.vnecdn.net/vnexpress/restruct/i/v372/logos/57x57.png"
//    18 => "https://s1.vnecdn.net/vnexpress/restruct/i/v372/logos/114x114.png"
//    19 => "https://s1.vnecdn.net/vnexpress/restruct/i/v372/v2_2019/pc/graphics/parten-tet.png"
//    20 => "https://s1.vnecdn.net/vnexpress/restruct/i/v372/v2_2019/pc/graphics/logo.svg"
//    21 => "https://s1.vnecdn.net/vnexpress/restruct/i/v372/v2_2019/pc/graphics/bg-dhd-pc.svg"
//  ]
//]
