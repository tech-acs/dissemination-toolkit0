import { Plugin } from '@ckeditor/ckeditor5-core';
import {
    Widget,
    toWidget,
    viewToModelPositionOutsideModelElement
} from '@ckeditor/ckeditor5-widget';
import VizCommand from './vizcommand.js';

export default class VizEditing extends Plugin {
    static get requires() {
        return [ Widget ];
    }

    init() {
        console.log( 'VizEditing#init() got called' );

        this._defineSchema();
        this._defineConverters();

        this.editor.commands.add( 'viz', new VizCommand( this.editor ) );

        this.editor.editing.mapper.on(
            'viewToModelPosition',
            viewToModelPositionOutsideModelElement( this.editor.model, viewElement => viewElement.hasClass( 'viz' ) )
        );

        this.editor.config.define( 'vizConfig', {
            types: [ 'date', 'first name', 'surname' ]
        } );
    }

    _defineSchema() {
        const schema = this.editor.model.schema;

        schema.register( 'viz', {
            // Behaves like a self-contained inline object (e.g. an inline image)
            // allowed in places where $text is allowed (e.g. in paragraphs).
            // The inline widget can have the same attributes as text (for example linkHref, bold).
            inheritAllFrom: '$inlineObject',

            // The placeholder can have many types, like date, name, surname, etc:
            allowAttributes: [ 'name' ]
        } );
    }

    _defineConverters() {
        const conversion = this.editor.conversion;

        conversion.for( 'upcast' ).elementToElement( {
            view: {
                name: 'span',
                classes: [ 'viz' ]
            },
            model: ( viewElement, { writer: modelWriter } ) => {
                // Extract the "name" from "{name}".
                const name = viewElement.getChild( 0 ).data.slice( 1, -1 );

                return modelWriter.createElement( 'viz', { name } );
            }
        } );

        conversion.for( 'editingDowncast' ).elementToElement( {
            model: 'viz',
            view: ( modelItem, { writer: viewWriter } ) => {
                const widgetElement = createVizView( modelItem, viewWriter );
                // Enable widget handling on a placeholder element inside the editing view.
                return toWidget( widgetElement, viewWriter );
            }
        } );

        conversion.for( 'dataDowncast' ).elementToElement( {
            model: 'viz',
            view: ( modelItem, { writer: viewWriter } ) => createVizView( modelItem, viewWriter )
        } );

        // Helper method for both downcast converters.
        function createVizView( modelItem, viewWriter ) {
            const name = modelItem.getAttribute( 'name' );

            const vizView = viewWriter.createContainerElement( 'span', {
                class: 'viz'
            } );

            // Insert the placeholder name (as a text).
            const innerText = viewWriter.createText( '{' + name + '}' );
            viewWriter.insert( viewWriter.createPositionAt( vizView, 0 ), innerText );

            return vizView;
        }
    }
}
