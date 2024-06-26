import { createGrid } from 'ag-grid-community';

export default class AgGridTable {
    id;
    rootElement;
    table;

    constructor(htmlId) {
        this.id = htmlId
        this.rootElement = document.getElementById(htmlId)
        let options = JSON.parse(this.rootElement.dataset['options'])
        if (options.rowData.length > 0) {
            this.rootElement.innerHTML = ''
            this.table = createGrid(this.rootElement, options);
        }
        console.log({htmlId, options, table: this.table})
        this.registerLivewireEventListeners();
    }

    registerLivewireEventListeners() {
        Livewire.on(`updateTable.${this.id}`, (event) => {
            let options
            [options] = event
            console.log('Table received data: ' + this.id, options);
            this.rootElement.innerHTML = ''

            options.columnDefs = options.columnDefs.map(colDef => {
                /*if (colDef?.type === 'numericColumn') {
                    colDef.valueFormatter = params => new Intl.NumberFormat().format(params.value)
                }*/
                if (colDef?.type === 'rangeColumn') {
                    colDef.comparator = (valueA, valueB, nodeA, nodeB, isDescending) => parseInt(valueA) - parseInt(valueB)
                }
                return colDef
            })

            this.table = createGrid(this.rootElement, options);
        });
    }

    resize() {
        const table = Alpine.raw(this.table)
        if (table) {
            table.sizeColumnsToFit()
        }
    }
}
