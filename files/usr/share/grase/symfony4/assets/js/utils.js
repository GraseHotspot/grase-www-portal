/**
 * Utils sources from various places on the net, all here for a handy place to dump them
 */



/**
 * Extract Query Params from URL
 *
 * http://css-tricks.com/snippets/javascript/get-url-variables/
 */

export function getQueryVariable(variable)
{
    var query = window.location.search.substring(1);
    var vars = query.split("&");
    for (var i=0;i<vars.length;i++) {
        var pair = vars[i].split("=");
        if(pair[0] == variable){return pair[1];}
    }
    return(false);
}
