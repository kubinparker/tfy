<?php
require './vendor/autoload.php';

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

    // lấy list url đã lưu vào file json trc đó thực hiện download video về máy
    getAndSaveVideos($json_video);
    echo json_encode(['success' => ['ok']]);
    exit();
}

// hàm download video
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

function getAndSaveVideos($json_file)
{
    $today = (new DateTime())->format('Ymd');
    $jsonVideo = file_get_contents($json_file);
    $dataVideo = json_decode($jsonVideo, true);

    $text_content = "";

    foreach ($dataVideo as $video) {
        if (!isset($video['videoid'][0]) || !isset($video['video'][0])) continue;
        $file_video = "./videos/movies/movie_" . $video['videoid'][0] . ".mp4";
        downloadUrlToFile($video['video']['0'], $file_video);
        $text_content .= "file " . $file_video . "\n";
    }
    // lưu trữ tên video đã được download về vào file text
    $list_video_name =  "./videos/join_video_name/list_video_name_" . $today . ".txt";
    file_put_contents($list_video_name, $text_content);

    return $list_video_name;
    // $result_file = "./videos/results/video_" . $today . ".mp4";
    // $cmd = "ffmpeg -f concat -i " . $list_video_name . " -c copy " . $result_file;

    // system($cmd, $status_code);
    // echo $status_code;

}


function dd($pr)
{
    echo '<pre>';
    var_dump($pr);
    echo '</pre>';
    exit();
}


function getFFMpeg($timeout = 3600, $threads = 12)
{
    return FFMpeg\FFMpeg::create([
        'ffmpeg.binaries' => './libs/ffmpeg',
        'ffprobe.binaries' => './libs/ffprobe',
        'temporary_directory' => './var/ffmpeg-tmp',
        'timeout'          => $timeout,
        'ffmpeg.threads'   => $threads
    ]);
}


// $duration: số giây được cắt ra
function getGifFromVideo($path, $from = 0, $duration = 10, $new_path = './images/start_with.gif')
{
    $ffmpeg = getFFMpeg();
    $video = $ffmpeg->open($path);
    $video
        ->gif(FFMpeg\Coordinate\TimeCode::fromSeconds($from), new FFMpeg\Coordinate\Dimension(640, 480), $duration)
        ->save($new_path);
}


function convertAndMerImageToGif()
{
    $frameDir = './frames/';
    $pth = $frameDir . '*.png';
    $frames = glob($frameDir . '*.png');
    $outputGif = 'output.gif';
    exec('/usr/local/bin/convert -delay 10 -loop 0 ' . $pth . ' ./' . $outputGif);

    // // Khởi tạo hoạt hình GIF
    // $imagick = new Imagick();

    // foreach ($frames as $i => $frame) {

    // if ($i < 24 || $i > 240) {
    //     echo $i . '<br>';
    //     exec('/usr/local/bin/convert ./' . $frame . ' -edge 1 ./' . $frame);
    // }
    // if (($i >= 24 && $i < 48) || ($i > 224 && $i < 240)) {
    //     echo $i . '<br>';
    //     exec('/usr/local/bin/convert ./ ' . $frame . ' -canny 0x1+10%+30% ./' . $frame);
    // }

    // $image = new Imagick($frame);

    // if (($i >= 48 && $i < 72) || ($i > 224 && $i < 240)) {
    //     echo $i . '<br>';
    //     exec('/usr/local/bin/convert ./ ' . $frame . ' -colorspace Gray -negate -edge 1 -negate ./' . $frame);
    //     // $image->edgeImage(2);
    // }
    // $imagick->addImage($image);

    // $image->clear();
    // $image->destroy();
    // }

    // $imagick->writeImages($outputGif, true);
    // $imagick->clear();
    // $imagick->destroy();
}

// download video về thành công thì xử lý cắt ghép chỉnh sửa video
try {
    $ffmpeg = getFFMpeg();

    convertAndMerImageToGif();

    // tao anh gif tu video
    // getGifFromVideo('./videos/movies/movie_7207264504210558235.mp4', 3, 7);
    // ket thuc

    // $video = $ffmpeg->open('./videos/movies/movie_7120770257710566699.mp4');
    // $array_video = ['./videos/movies/movie_7120770257710566699.mp4', './videos/movies/movie_7207264504210558235.mp4', './videos/movies/movie_7212813733624630571.mp4', './videos/movies/movie_7213657867071606018.mp4'];

    // $format = new FFMpeg\Format\Video\X264();
    // $format->setAudioCodec("libmp3lame");

    // ghép nhiều video 
    // $video
    //     ->concat($array_video)
    //     ->saveFromDifferentCodecs($format, './videos/results/merge/movie_7120770247710566699_7207264504210558235.mp4');
    // kết thúc 


    // ghép nhiều video và thêm hiệu ứng

    // $advancedMedia = $ffmpeg->openAdvanced($array_video);
    // $advancedMedia->filters()
    //     ->custom('[0:v]', 'negate', '[v0negate]')
    //     ->custom('[1:v]', 'edgedetect', '[v1edgedetect]')
    //     ->custom('[2:v]', 'hflip', '[v2hflip]')
    //     ->custom('[3:v]', 'vflip', '[v3vflip]')
    //     ->xStack('[v0negate][v1edgedetect][v2hflip][v3vflip]', FFMpeg\Filters\AdvancedMedia\XStackFilter::LAYOUT_2X2, 4, '[resultv]');
    // $advancedMedia
    //     ->map(array('[resultv]'), $format, './output3.mp4')
    //     ->save();
    // kết thúc 

    // thêm logo ở góc trên bên trái

    // $logo_png = './images/logo100_100.png';
    // $video = $ffmpeg->open('./videos/results/merge/movie_7120770247710566699_7207264504210558235.mp4');
    // $video
    //     ->filters()
    //     ->watermark($logo_png, array(
    //         'position' => 'absolute',
    //         'x' => 5,
    //         'y' => 5,
    //     ));

    // kết thúc 


    // thêm ảnh gif dưới góc phải -> nhưng chưa chạy được toàn bộ video. ảnh gif chỉ mới chạy được hết 1 vòng đời của ảnh gif đó. cần nghiên cứu lặp lại

    // $logo_gif = './images/logo.gif';
    // ->watermark($logo_gif, array(
    //     'position' => 'relative',
    //     'bottom' => 5,
    //     'right' => 5,
    // ));

    // kết thúc 

    // $video->save($format, './videos/results/add_icon/movie_7120770247710566699_7207264504210558235.mp4');


    // chia 1/2 màn hình (trái-phải) của 2 video cùng chạy. -> cần kiểm tra thời lượng của 2 video 

    // $advancedMedia = $ffmpeg->openAdvanced($array_video);
    // $advancedMedia->filters()
    //     ->custom('[0:v][1:v]', 'hstack', '[v]');

    // $advancedMedia
    //     ->map(array('0:a', '[v]'), new FFMpeg\Format\Video\X264('aac', 'libx264'), './output.mp4')
    //     ->save();

    // kết thúc 

    // gep nhac vao video

    // 1. lay thoi gian doan nhac
    // $inputAudioPath = __DIR__ . '/audios/thu-cuoi2.mp3';
    // // $ffprobe = FFMpeg\FFProbe::create(['ffprobe.binaries' => './libs/ffprobe']);
    // // $audioDuration = $ffprobe
    // //     ->format($inputAudioPath)
    // //     ->get('duration');
    // // kết thúc 1.

    // // 2. ghep va lap video cho den het nhac
    // $inputVideoPath = __DIR__ . '/videos/results/merge/movie_7120770247710566699_7207264504210558235.mp4';

    // $outputPath = __DIR__ . '/videos/results/outputVideo4.mp4';

    // $loop = ceil(504 / 22);
    // // ghep dc nhac vao nhung video chua lap lai den het nhac
    // // su dung opt "-shortest" de dong bo nhac va video. cai nao ngan hon se dc uu tien. chay het se dung` 
    // shell_exec(__DIR__ . "/libs/ffmpeg -stream_loop " . $loop . " -i " . $inputVideoPath . " -i " . $inputAudioPath . " -c:v copy -c:a aac -map 0:v:0 -map 1:a:0 " . $outputPath);

    // kết thúc 2.
    // ket thuc

} catch (Exception $e) {
    print_r($e->getMessage());
}

// chỉnh sửa xong up lên YT check bản quyền = api

// nếu ok đăng chính thức = api
