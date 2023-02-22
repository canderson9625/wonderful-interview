let mapStyles = [
    {
        "featureType": "all",
        "elementType": "labels.text.fill",
        "stylers": [
            {
                "color": "#ffffff"
            },
            {
                "weight": "0.20"
            },
            {
                "lightness": "28"
            },
            {
                "saturation": "23"
            },
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "all",
        "elementType": "labels.text.stroke",
        "stylers": [
            {
                "color": "#494949"
            },
            {
                "lightness": 13
            },
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "all",
        "elementType": "labels.icon",
        "stylers": [
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "administrative",
        "elementType": "geometry.fill",
        "stylers": [
            {
                "color": "#000000"
            }
        ]
    },
    {
        "featureType": "administrative",
        "elementType": "geometry.stroke",
        "stylers": [
            {
                "color": "#144b53"
            },
            {
                "lightness": "-11"
            },
            {
                "weight": 1.4
            },
            {
                "visibility": "on"
            }
        ]
    },
    {
        "featureType": "landscape",
        "elementType": "all",
        "stylers": [
            {
                "color": "#08304b"
            },
            {
                "lightness": "-13"
            }
        ]
    },
    {
        "featureType": "poi",
        "elementType": "geometry",
        "stylers": [
            {
                "color": "#000203"
            },
            {
                "lightness": "14"
            }
        ]
    },
    {
        "featureType": "road.highway",
        "elementType": "geometry.fill",
        "stylers": [
            {
                "color": "#000000"
            }
        ]
    },
    {
        "featureType": "road.highway",
        "elementType": "geometry.stroke",
        "stylers": [
            {
                "color": "#010304"
            },
            {
                "lightness": 25
            },
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "road.arterial",
        "elementType": "geometry.fill",
        "stylers": [
            {
                "color": "#000000"
            }
        ]
    },
    {
        "featureType": "road.arterial",
        "elementType": "geometry.stroke",
        "stylers": [
            {
                "color": "#0b3d51"
            },
            {
                "lightness": 16
            }
        ]
    },
    {
        "featureType": "road.local",
        "elementType": "geometry",
        "stylers": [
            {
                "color": "#000000"
            }
        ]
    },
    {
        "featureType": "transit",
        "elementType": "all",
        "stylers": [
            {
                "color": "#146474"
            }
        ]
    },
    {
        "featureType": "water",
        "elementType": "all",
        "stylers": [
            {
                "color": "#021019"
            }
        ]
    }
]

//submissionObject is localized
document.querySelector('.input-group input')?.addEventListener("change", 
    function confirmSubmission() {
        let csv = this.files[0];
        const ajaxData = new FormData();
        ajaxData.append( 'action', 'submission_create');
        ajaxData.append( 'nonce', submissionObject.nonce);
        ajaxData.append('submission', csv, csv.name);
        const request = new Request(submissionObject.ajaxurl, {method: 'POST', body: ajaxData});
        if ( confirm("Would you like to submit your Uploaded CSV?") ) {
            fetch(request)
                .then(res => res.text() )
                .then(data => { window.location.href += "/submission/"+data });
        } else {
            this.value = null;
            document.getElementById("file-input").innerText="Select File";
        }
    }
);

//popuplate the map on the submission page
window.addEventListener("load", () => {

    if (!document.querySelector('body.single-submission')) {
        return;
    }

    let csvLines = post_meta.data[0].split("\r");
    let headers = csvLines[0].split(",");
    let result = [];

    for ( let i = 1; i < csvLines.length; i++ ) {
        let obj = {};
        let currentLine=csvLines[i].split(",");

        for ( let j = 0; j < headers.length; j++ ) {
            obj[headers[j]] = currentLine[j];
        }

        result.push(obj);
    }

    let output = JSON.parse( JSON.stringify(result) );

    const center = { lat: 40, lng: -101.2996 };
    const map = new google.maps.Map(
        document.getElementById("map"),
        {
            zoom: 5,
            center: center,
            styles: mapStyles,
        }
    );

    Object.entries(output).forEach( ([key, val]) => {
        let coords = {lat: Number(val.Latitude), lng: Number(val.Longitude)};

        const contentString =
            '<div id="content" style="color: black;">' +
            '<p style="margin: 0; margin-bottom: 5px;"><strong>' + val["Airport Name"] + '</strong></p>' +
            '<p style="margin: 0;">'+ val.Latitude + ', ' + val.Longitude + '</p>' +
            "</div>";
    
        const infowindow = new google.maps.InfoWindow({
            content: contentString,
            ariaLabel: val["Airport Name"],
        });

        let marker = new google.maps.Marker({
            position: coords,
            map,
            title: val["Airport Name"],
            icon: submissionObject.theme_path + '/assets/img/marker.png'
        });

        marker.addListener("click", () => {
            infowindow.open({
                anchor: marker,
                map,
            })
        });
    });
});

//copy link from input
document.querySelector('#share')?.addEventListener("click", 
    function copyValue(e) {
        this.select();
        this.setSelectionRange(0, 99999);

        navigator.clipboard.writeText(this.value);
        alert("Link copied to clipboard.");
    }
);
