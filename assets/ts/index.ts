// console.log("hello world")
import * as mapStyles from "../map-styles.json"

// console.log(mapStyles);

// Initialize and add the map
function initMap(): void {
    // The location of Uluru
    const uluru = { lat: -25.344, lng: 131.031 };
    // The map, centered at Uluru
    const map = new google.maps.Map(
        document.getElementById("map") as HTMLElement,
        {
            zoom: 4,
            center: uluru,
            styles: mapStyles,
        }
    );

    // The marker, positioned at Uluru
    const marker = new google.maps.Marker({
        position: uluru,
        map: map,
    });
}

export {};

declare global {
    interface Window {
        initMap: () => void;
    }
}
window.initMap = initMap;
