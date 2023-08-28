<?php
$today = (new DateTime())->format('Ymd');
$json_file = "./data/data_tiktok_" . $today . ".json";
// dữ liệu truyền từ console của tiktok.com về
if ($_POST['datas']) {
    // lưu vào thành file json có tên là data/data_tiktok_<date>.json
    // xử lý lưu file ở đây

    $fp = fopen($json_file, 'w');
    fwrite($fp, $_POST['datas']);
    fclose($fp);

    echo json_encode(['success' => ['ok']]);
    exit();
}

// từ trang https://savetiktok.io/ sẽ gọi về để lấy danh sách id video
if (isset($_GET['list_id']) && intval($_GET['list_id']) == 1) {
    // lấy danh sách id video từ file data/data_tiktok_<date>.json
    // xử lý lấy data ở đây
    $data = [];
    $json = file_get_contents($json_file);
    $data = json_decode($json, true);
    // echo json_encode(['data' => $data]);
    echo json_encode(['data' => $data]);
    exit();
}

$json_video = "./videos/json/data_tiktok_" . $today . ".json";
// dữ liệu truyền từ console của https://savetiktok.io/ về
if ($_POST['videos']) {
    // lưu vào thành file json có tên là ./videos/json/data_tiktok_<date>.json
    // xử lý lưu file ở đây
    $fp = fopen($json_video, 'w');
    fwrite($fp, $_POST['videos']);
    fclose($fp);

    echo json_encode(['success' => ['ok']]);
    exit();
}


function downloadUrlToFile($url, $outFileName)
{
    if (is_file($url)) {
        copy($url, $outFileName);
    } else {
        $options = array(
            CURLOPT_FILE    => fopen($outFileName, 'w'),
            CURLOPT_TIMEOUT =>  28800, // set this to 8 hours so we dont timeout on big files
            CURLOPT_URL     => $url
        );

        $ch = curl_init();
        curl_setopt_array($ch, $options);
        curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $httpcode;
    }
}


// lấy list url đã lưu vào file json trc đó thực hiện download video về máy
// $jsonVideo = file_get_contents($json_video);
// $dataVideo = json_decode($jsonVideo, true);

// $text_content = "";
// foreach ($dataVideo as $video) {
//     if (!isset($video['videoid'][0]) || !isset($video['video'][0])) continue;
//     $file_video = "./videos/movies/movie_" . $video['videoid'][0] . ".mp4";
//     downloadUrlToFile($video['video']['0'], $file_video);
//     $text_content .= "file " . $file_video . "\n";
// }
$list_video_name =  "./videos/join_video_name/list_video_name_" . $today . ".txt";
// file_put_contents($list_video_name, $text_content);
$result_file = "./videos/results/video_" . $today . ".mp4";
$cmd = "ffmpeg -f concat -i " . $list_video_name . " -c copy " . $result_file;

system($cmd, $status_code);
echo $status_code;
// download video về thành công thì xử lý cắt ghép chỉnh sửa video


// chỉnh sửa xong up lên YT check bản quyền = api

// nếu ok đăng chính thức = api
