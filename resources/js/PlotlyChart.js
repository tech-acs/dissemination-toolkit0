import Plotly from 'plotly.js-dist';
import fr from 'plotly.js-locales/fr';
import ptPT from 'plotly.js-locales/pt-pt';

export default class PlotlyChart {
    id;
    rootElement;
    data = [];
    layout = {};
    config = [];

    async fetchData(vizId) {
        const response = await axios.get(`/api/visualization/${vizId}`);
        console.log('Fetched chart via axios:', response.data);
        this.data = response.data.data;
        this.layout = response.data.layout;
        this.config = response.data.config;
    }

    constructor(htmlId) {
        this.id = htmlId
        this.rootElement = document.getElementById(htmlId)

        if (this.config.locale === 'fr') {
            Plotly.register(fr);
        } else if (this.config.locale === 'pt') {
            Plotly.register(ptPT);
        }

        const vizId = this.rootElement.getAttribute('viz-id')
        console.log({vizId})
        this.rootElement.innerHTML = ''
        if (vizId) {
            this.fetchData(vizId)
                .then(() => {
                    console.log({data: this.data});
                    Plotly.newPlot(this.rootElement, this.data, this.layout, this.config);
                })
        } else {
            this.data = JSON.parse(this.rootElement.dataset['data'])
            this.layout = JSON.parse(this.rootElement.dataset['layout'])
            this.config = JSON.parse(this.rootElement.dataset['config'])
            Plotly.newPlot(this.rootElement, this.data, this.layout, this.config);
        }
        console.log({data:this.data, layout:this.layout});
        this.registerLivewireEventListeners();
    }

    registerLivewireEventListeners() {
        Livewire.on(`updateResponse.${this.id}`, (dataAndLayout) => {
            console.log('Received updateResponse: ' + this.id, dataAndLayout);
            Plotly.react(this.id, ...dataAndLayout, this.config)
        });
    }
}
