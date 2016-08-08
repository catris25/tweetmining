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

 function init() {
     loadJSON(function(response) {
      // Parse JSON string into object
        var actual_JSON = JSON.parse(response);
        var data = new Array(7);
        for(var i = 0; i < 7; i++) {
            data[i] = new Array(2);
            data[i][0] = actual_JSON.statuses[i].id;
            console.log(typeof data[i][0]);
            //data[i][0] = Number(data[i][0]);
            //console.log(typeof data[i][0]);

            data[i][1] = actual_JSON.statuses[i].text.replace(/(?:https?|ftp):\/\/[\n\S]+/g, '');
            data[i][1] = data[i][1].replace(/\B@[a-z0-9_-]+/gi, '');
            data[i][1] = data[i][1].replace(/[.,\/#!$%\^&\*;:{}=\-_`~()]/g, '');
            data[i][1] = data[i][1].replace(/\s{2,}/g, ' ');
            data[i][1] = data[i][1].replace(/[^\w.,\s]/g, '');
            data[i][1] = data[i][1].replace(/[0-9]/g, '');
            // $.each(statuses, function(i, value) {
            //     console.log(statuses[i], value);
            // });

             $("#demo").append(data[i][0] + " <br>by " + data[i][1] + "<br><br>");
        }

        //var fname = "results.csv";
        var csvContent = "";
        data.forEach(function(info, index){
          dataString = info.join(",");
          csvContent += index < data.length ? dataString+ "\n" : dataString;
        });
        
        // var encodedUri = encodeURI(csvContent);
        // window.open(encodedUri);


         //download(csvContent, 'datafix.csv', 'text/csv');

     });
}

var download = function(content, fileName, mimeType) {
  var a = document.createElement('a');
  mimeType = mimeType || 'application/octet-stream';

  if (navigator.msSaveBlob) { // IE10
    return navigator.msSaveBlob(new Blob([content], { type: mimeType }), fileName);
  } else if ('download' in a) { //html5 A[download]
    a.href = 'data:' + mimeType + ',' + encodeURIComponent(content);
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
    f.src = 'data:' + mimeType + ',' + encodeURIComponent(content);

    setTimeout(function() {
      document.body.removeChild(f);
    }, 333);
    return true;
  }
}

function traverse(callback) {
    loadJSON(function(response){
        JSON.parse(response, function (key, value) {
            if (key !== '') {
              callback.call(this, key, value)
              //console.log(value);
            }
            return value
        });
    });
}

// function traverse (json, callback) {
//   JSON.parse(json, function (key, value) {
//     if (key !== '') {
//       callback.call(this, key, value)
//     }
//     return value
//   })
// }

function gotraverse() {
    traverse(function (key, value) {
        console.log(arguments);
        
        $("#demo").append(JSON.stringify(arguments)+"<br>");
        
    })
}
