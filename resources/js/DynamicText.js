export default class DynamicText {
    id;
    rootElement;

    constructor(htmlId) {
        this.id = htmlId
        //this.rootElement = document.getElementById(htmlId)

        //this.rootElement.innerHTML = ''
        this.registerLivewireEventListeners();
    }

    registerLivewireEventListeners() {
        Livewire.on(`filterChanged`, (filter) => {
            let filterPath, areaName
            [filterPath, areaName] = filter
            console.log({name: areaName, me: this.id})
        });
    }
}
