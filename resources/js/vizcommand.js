import { Command } from '@ckeditor/ckeditor5-core';
import {toWidget} from "@ckeditor/ckeditor5-widget";

export default class VizCommand extends Command {
    execute( { value } ) {
        const editor = this.editor;
        const selection = editor.model.document.selection;

        editor.model.change( writer => {
            // Create a <placeholder> element with the "name" attribute (and all the selection attributes)...
            const viz = writer.createElement( 'viz', {
                ...Object.fromEntries( selection.getAttributes() ),
                name: value
            } );

            // ... and insert it into the document. Put the selection on the inserted element.
            editor.model.insertObject( viz, null, null, { setSelection: 'on' } );
        } );
    }

    refresh() {
        const model = this.editor.model;
        const selection = model.document.selection;

        const isAllowed = model.schema.checkChild( selection.focus.parent, 'viz' );

        this.isEnabled = isAllowed;
    }
}
