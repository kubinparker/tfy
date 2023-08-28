
async function fetchData ( url )
{
    const response = await fetch( "https://savetiktok.io/api/video-info", {
        method: "POST",
        body: JSON.stringify( {
            url: url,
        } ),
        headers: {
            "Content-type": "application/json; charset=UTF-8"
        }
    } );
    const data = await response.json();
    return data;
}


async function sleep ( ms )
{
    return new Promise( resolve => setTimeout( resolve, ms ) );
}


async function processRequests ( urls, delay )
{
    var dt = [];
    for ( const url of urls )
    {
        const result = await fetchData( url );
        dt.push( result );
        await sleep( delay );
    }
    return dt;
}


fetch( 'http://make.coint.tfy/_index.php?list_id=1' ).then( r => r.json() ).then( async ( d ) =>
{
    const urls = d.data.map( v => `https://www.tiktok.com/@${ v.author }/video/${ v.id }` );
    var videos = await processRequests( urls, 10000 );

    // trả video về file _index.php
    var formData = new FormData();
    formData.append( 'videos', JSON.stringify( videos ) );
    const options = {
        method: 'POST',
        body: formData
    };
    fetch( 'http://make.coint.tfy/_index.php', options ).then( ( res ) => res.json() ).then( data => console.log( data.success ) );
} );