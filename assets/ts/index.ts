// Handle Google maps styles
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
function initMap(): void {
    
    const center = { lat: 40, lng: -101.2996 };
    
    const map = new google.maps.Map(
        document.getElementById("map") as HTMLElement,
        {
            zoom: 5,
            center: center,
            styles: mapStyles,
        }
    );
    
    const marker = new google.maps.Marker({
        position: center,
        map: map,
    });
}

//declare our window closure
export {};
declare global {
    interface Window {
        initMap: () => void;
    }
}
//dont run init unless on the homepage
if (document.querySelector('body.home')){
    window.initMap = initMap;
}


//use button visually and store data with input
const button = document.getElementById("file-input");
const input: HTMLInputElement | null = document.querySelector(".input-group input");
const dropZone = document.querySelector('.modal');

document.addEventListener("dragover", function(event) {
    event.preventDefault();
});

if (dropZone !== null) {
    dropZone.addEventListener("drop", (e) => {dropHandler(e as DragEvent)});

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false)
    });
}

function preventDefaults (e: Event) {
    e.preventDefault()
    e.stopPropagation()
}

button?.addEventListener("click", triggerInputClick);
input?.addEventListener("change", updateFileUploadText);

function triggerInputClick() {    
    input?.click();
}

function updateFileUploadText() {
    if (input !== null && input.files !== null && input?.files[0]?.name !== null && button !== null) {
        button.innerText=input?.files[0]?.name || "Select File";
    }
}

function dropHandler(file: DragEvent) {
    if (input !== null && file.dataTransfer !== null && file.dataTransfer?.files[0].type === "text/csv") {
        input.files=file.dataTransfer.files;
    } else {
        alert("Please upload a CSV file.");
    }
    updateFileUploadText();
}