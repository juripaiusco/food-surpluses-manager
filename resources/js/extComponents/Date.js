export function __date(dateString) {

    const date = new Date(dateString);
    // Then specify how you want your dates to be formatted
    let y = new Intl.DateTimeFormat('default', { year: 'numeric' }).format(date);
    let m = new Intl.DateTimeFormat('default', { month: '2-digit' }).format(date);
    let d = new Intl.DateTimeFormat('default', { day: '2-digit' }).format(date);
    let h = new Intl.DateTimeFormat('default', { hour: '2-digit' }).format(date);
    let i = new Intl.DateTimeFormat('default', { minute: '2-digit' }).format(date);
    let s = new Intl.DateTimeFormat('default', { second: '2-digit' }).format(date);

    if (s < 10) {
        s = '0' + s;
    }

    return d + '/' + m + '/' + y + ' ' + h + ':' + i + ':' + s;

}
