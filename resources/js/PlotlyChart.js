import Plotly from 'plotly.js-dist';
import fr from 'plotly.js-locales/fr';
import ptPT from 'plotly.js-locales/pt-pt';

export default class PlotlyChart {
    id;
    rootElement;
    vizId;
    data = [];
    layout = {};
    config = [];
    filterable = false;

    emptyLayout = {
        xaxis: {visible: false},
        yaxis: {visible: false},
        plot_bgcolor: '#f1f5f9',
        annotations: [{
            text: 'There is no data for selected area',
            xref: 'paper',
            yref: 'paper',
            showarrow: false,
            font: {size: 28}
        }]
    }

    async fetchData(vizId, filterPath = '') {
        const response = await axios.get(`/api/visualization/${vizId}?path=${filterPath}`);
        console.log('Fetched chart via axios:', response.data);
        this.data = response.data.data;
        this.layout = response.data.layout;
        this.config = response.data.config;
        this.filterable = response.data.filterable;
    }

    constructor(htmlId) {
        this.id = htmlId
        this.rootElement = document.getElementById(htmlId)

        if (this.config.locale === 'fr') {
            Plotly.register(fr);
        } else if (this.config.locale === 'pt') {
            Plotly.register(ptPT);
        }

        this.vizId = this.rootElement.getAttribute('viz-id')
        this.rootElement.innerHTML = ''
        if (this.vizId) {
            this.fetchData(this.vizId)
                .then(() => {
                    console.log({data: this.data});
                    Plotly.newPlot(this.rootElement, this.data, this.layout, this.config);
                    this.registerLivewireEventListeners(this.filterable);
                })
        } else {
            this.data = JSON.parse(this.rootElement.dataset['data'])
            this.layout = JSON.parse(this.rootElement.dataset['layout'])
            this.config = JSON.parse(this.rootElement.dataset['config'])
            Plotly.newPlot(this.rootElement, this.data, this.layout, this.config);
            this.registerLivewireEventListeners(this.filterable);
        }
    }

    registerLivewireEventListeners(filterable) {
        Livewire.on(`updateResponse.${this.id}`, (dataAndLayout) => {
            console.log('Received updateResponse: ' + this.id, dataAndLayout);
            Plotly.react(this.id, ...dataAndLayout, this.config)
        });

        if (filterable) {
            Livewire.on(`filterChanged`, (filter) => {
                let filterPath
                [filterPath] = filter
                this.fetchData(this.vizId, filterPath)
                    .then(() => {
                        console.log({Path: filterPath, Filtered: this.data});
                        if (! this.data.length) {
                            Plotly.react(this.rootElement, this.data, this.emptyLayout);
                        } else {
                            Plotly.react(this.rootElement, this.data, this.layout);
                        }
                    })
            });
        }
    }
}
