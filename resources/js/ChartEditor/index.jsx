import ReactDOM from 'react-dom';
import ChartEditor from "./ChartEditor.jsx";
import './index.css';
import { has } from "lodash";

const rootElement = document.getElementById('chart-editor')

if (rootElement) {
    const response = await axios.get('/manage/viz-builder-wizard/api/get');
    console.log('Fetched initial:', response.data);
    let data = response.data.data ?? [];

// ToDo: Do this for all traces and even for z... axis
    if (data && has(data[0], 'meta.columnNames')) {
        data[0].x = response.data.dataSources[data[0].xsrc];
        data[0].y = response.data.dataSources[data[0].ysrc];
    }

    const vizId = 1

    ReactDOM.render(<ChartEditor
        dataSources={response.data.dataSources}
        initialData={data}
        initialLayout={response.data.layout}
        config={response.data.config}
        vizId={vizId}
        indicatorTitle={'Some title'}
        defaultLayout={[]}
    />, rootElement);
}
