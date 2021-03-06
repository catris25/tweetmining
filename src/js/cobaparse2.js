function loadJSON(callback) {   

    var xobj = new XMLHttpRequest();
        xobj.overrideMimeType("application/json");
    xobj.open('GET', 'tweets_kecelakaan.json', true); // Replace 'my_data' with the path to your file
    xobj.onreadystatechange = function () {
          if (xobj.readyState == 4 && xobj.status == "200") {
            // Required use of an anonymous callback as .open will NOT return a value but simply returns undefined in asynchronous mode
            callback(xobj.responseText);
          }
    };
    xobj.send(null);  
 }

var arr = new Array(7);
var csvContent = "";
function init() {
     loadJSON(function(response) {
      // Parse JSON string into object
        var actual_JSON = JSON.parse(response);
        var arr = new Array(7);
        for(var i = 0; i < 7; i++) {
            arr[i] = new Array(2);
            arr[i][0] = actual_JSON.statuses[i].id;
            arr[i][1] = actual_JSON.statuses[i].text;
            // $.each(statuses, function(i, value) {
            //     console.log(statuses[i], value);
            // });

             $("#demo").append(arr[i][0] + " <br>by " + arr[i][1] + "<br><br>");
        }

        arr.forEach(function(info, index){
          dataString = info.join(",");
          csvContent += index < arr.length ? dataString+ "\n" : dataString;
        });
        
        // var encodedUri = encodeURI(csvContent);
        // window.open(encodedUri);
        download(csvContent, 'csv_file.csv', 'text/csv');

     });
}

var download = function(content, fileName, mimeType) {
  var a = document.createElement('a');
  mimeType = mimeType || 'application/octet-stream';

  if (navigator.msSaveBlob) { // IE10
    return navigator.msSaveBlob(new Blob([content], { type: mimeType }), fileName);
  } else if ('download' in a) { //html5 A[download]
    a.href = 'arr:' + mimeType + ',' + encodeURIComponent(content);
    a.setAttribute('download', fileName);
    document.body.appendChild(a);
    setTimeout(function() {
      a.click();
      document.body.removeChild(a);
    }, 66);
    return true;
  } else { //do iframe dataURL download (old ch+FF):
    var f = document.createElement('iframe');
    document.body.appendChild(f);
    f.src = 'arr:' + mimeType + ',' + encodeURIComponent(content);

    setTimeout(function() {
      document.body.removeChild(f);
    }, 333);
    return true;
  }
}

