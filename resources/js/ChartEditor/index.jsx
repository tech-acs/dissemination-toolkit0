import ReactDOM from 'react-dom';
import ChartEditor from "./ChartEditor.jsx";
import './index.css';
import { has } from "lodash";

const rootElement = document.getElementById('chart-editor')

if (rootElement) {
    const response = await axios.get('/manage/viz-builder/chart/api/get');
    console.log('Fetched initial:', response.data);

    let data = response.data.initialData ?? [];
    // ToDo: Do this for all traces and even for z... axis. Also, it breaks when there are multi-columns on same axis
    if (data && has(data[0], 'meta.columnNames')) {
        data[0].x = response.data.dataSources[data[0].xsrc];
        data[0].y = response.data.dataSources[data[0].ysrc];
    }

    ReactDOM.render(<ChartEditor
        dataSources={response.data.dataSources}
        initialData={response.data.initialData}
        initialLayout={response.data.initialLayout}
        config={response.data.config}
        defaultLayout={response.data.defaultLayout}
    />, rootElement);
}
