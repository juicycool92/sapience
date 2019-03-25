const _ISNULL = ( data )=>{
    if ( typeof data ==='undefined' || data === '')
        return true;
    return false;
}
const _DATETIME_OPTION = {
    year:"numeric",
    month:"2-digit",
    day:"2-digit",
    hour:"numeric",
    minute:"numeric",
    second:"numeric"
};