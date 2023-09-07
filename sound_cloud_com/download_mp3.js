// client id : Iy5e1Ri4GTNgrafaXe4mLpmJLXbXEfBR
// sử dụng url của key [transcodings] trong biến window.__sc_hydration
// ex: https://api-v2.soundcloud.com/media/soundcloud:tracks:404486145/c6588c91-2109-4c1b-a2ef-a1153010b230/stream/hls?client_id=Iy5e1Ri4GTNgrafaXe4mLpmJLXbXEfBR
// response : {
//     url: "https://cf-hls-media.sndcdn.com/playlist/YpxCMRr8EP4J.128.mp3/playlist.m3u8?Policy=eyJTdGF0ZW1lbnQiOlt...zkzfX19XX0_&Signature=EHNSQO-TXXUP6aXmroMYKRU....v~w__&Key-Pair-Id=APKAI6TU7MMXM5DG6EPQ"
// }
// sử dụng url từ response trả về để lấy downfile playlist.m3u8
// trong file này sẽ chứa nhiều đường dẫn đc cắt ra từ 1 file nhạc
// tải hết về ghép lại sẽ được 1 file nhạc hoàn chỉnh


