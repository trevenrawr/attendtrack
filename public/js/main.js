function ajax(url, callback, errorCB, data) {
    'use strict';
    var xmlhttp = new XMLHttpRequest(), query = [], key;

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState === 4) {
            if (xmlhttp.status === 200) {
                callback(xmlhttp.responseText);
            } else {
                errorCB(xmlhttp.responseText);
            }
        }
    };
    
    for (key in data) {
        if (data.hasOwnProperty(key)) {
            query.push(encodeURIComponent(key) + '=' + encodeURIComponent(data[key]));
        }
    }
    
    xmlhttp.open('POST', url, true);
    xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xmlhttp.send(query.join('&'));
}


function dlist(id, dlid) {
    'use strict';
    var text, dataList, cb, ecb, data;
    
    text = document.getElementById(id);
    
    if (text.value.length === 3) {
        dataList = document.getElementById(dlid);

        cb = function (input) {
            var list = JSON.parse(input);
            while (dataList.lastChild) {
                dataList.removeChild(dataList.lastChild);
            }

            list.forEach(function (item) {
                var opt = document.createElement('option');
                opt.value = item.name;
                dataList.appendChild(opt);
            });
        };

        ecb = function (err) {
            window.alert('Something went wrong!    ' + err);
        };

        data = {'q': text.value};

        ajax('/T/names', cb, ecb, data);
    }
}


function addPres() {
    'use strict';
    var plist;
    
    plist = document.getElementById('pList');
    
    window.alert(plist.children.length);
}