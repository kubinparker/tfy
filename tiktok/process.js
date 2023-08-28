/**
 * bước 1:  vào trang tiktok.com
 * bước 2: mở console và nhập vào các lệnh bên dưới
 * 
*/

var __data__ = ( JSON.parse( document.querySelector( '#SIGI_STATE' ).text ) ).ItemModule || false;
var _data_ = [];

if ( !__data__ )
{
    boxSearch.forEach( e =>
    {
        const ec2 = e.querySelector( 'div[data-e2e="search_top-item"]' );
        let ahref = ec2.querySelector( 'a' ).href;
        ahref = ahref.replace( 'https://www.tiktok.com/@', '' );
        const [ author, v, id ] = ahref.split( '/' );
        var result = { author, id, desc: '' };

        const hashtags = e.querySelectorAll( 'a[data-e2e="search-common-link"]' );
        hashtags.forEach( e2 =>
        {
            let a2href = e2.getAttribute( 'href' );
            const [ _, t, hashtag ] = a2href.split( '/' );
            result.desc += ` #${ hashtag }`;
        } )

        _data_.push( result );
    } );

} else
{
    _data_ = ( Object.values( __data__ ) ).map( dt => ( { author: dt.author, id: dt.id, desc: dt.desc } ) );
}

var formData = new FormData();
formData.append( 'datas', JSON.stringify( _data_ ) );

const options = {
    method: 'POST',
    body: formData
};


fetch( 'http://make.coint.tfy/_index.php', options ).then( ( res ) => res.json() ).then( data => console.log( data.success ) );



/***
 * khi gặp lỗi https -> http
 * bước 1: bấm vào icon ổ khoá cạnh domain 
 * bước 2: chọn "cài đặt"
 * bước 3: trong mục Private & Security tìm đến "nội dung không an toàn"(hình tam giác có dấu chấm than) và chọn cho phép  
*/
