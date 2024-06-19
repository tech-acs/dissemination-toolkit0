import Plotly from 'plotly.js-dist';
import fr from 'plotly.js-locales/fr';
import ptPT from 'plotly.js-locales/pt-pt';

export default class PlotlyChart {
    id;
    data = [];
    layout = {};
    config = [];

    async fetchChartData(indicatorId) {
        const response = await axios.get(`/api/indicator/${indicatorId}`);
        console.log('Fetched initial:', response.data);
        this.data = response.data.data;
        this.layout = response.data.layout;
        this.config = []
    }

    constructor(rootElementId) {
        this.id = rootElementId;
        const el = document.getElementById(this.id)
        const indicatorId = el.getAttribute('indicator-id');

        if (this.config.locale === 'fr') {
            Plotly.register(fr);
        } else if (this.config.locale === 'pt') {
            Plotly.register(ptPT);
        }

        this.fetchChartData(indicatorId)
            .then(() => {
                console.log({data: this.data});
                Plotly.newPlot(el, this.data, this.layout, this.config);
            })

        console.log('1 - (alpine init), 2 - PlotlyChart constructor with id: ' + this.id);
        //this.registerLivewireEventListeners();
    }

    registerLivewireEventListeners() {
        Livewire.on(`updateResponse.${this.id}`, (dataAndLayout) => {
            console.log('3 - Received updateResponse: ' + this.id, dataAndLayout);
            Plotly.react(this.id, ...dataAndLayout, this.config)
        });
    }
}
