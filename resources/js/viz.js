import { Plugin } from '@ckeditor/ckeditor5-core';

import VizEditing from "./vizediting";
import VizUI from "./vizui";

export default class Viz extends Plugin {
    static get requires() {
        return [ VizEditing, VizUI ];
    }
}
