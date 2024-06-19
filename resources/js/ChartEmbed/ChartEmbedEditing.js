import { Plugin } from '@ckeditor/ckeditor5-core';
import { Widget, toWidget } from '@ckeditor/ckeditor5-widget';
import ChartEmbedCommand from './ChartEmbedCommand.js';

export default class ChartEmbedEditing extends Plugin {
    constructor( editor ) {
        super( editor );
    }

    static get requires() {
        return [ Widget ];
    }

    init() {
        this._defineSchema();
        this._defineConverters();

        this.editor.commands.add( 'chartEmbed', new ChartEmbedCommand( this.editor ) );
    }

    _defineSchema() {
        const schema = this.editor.model.schema;

        schema.register( 'chartEmbedElement', {
            inheritAllFrom: '$inlineObject',
            allowAttributes: [ 'indicator-id', 'id', 'x-data', 'x-init' ]
        } );
    }

    _defineConverters() {
        const editor = this.editor;

        editor.conversion.for( 'upcast' ).elementToElement( {
            view: {
                name: 'span',
                attributes: [ 'indicator-id' ]
            },
            model: ( viewElement, { writer } ) => {
                const externalUrl = viewElement.getAttribute( 'indicator-id' );

                return writer.createElement( 'chartEmbedElement', {
                    'indicator-id': externalUrl
                } );
            }
        } );

        editor.conversion.for( 'dataDowncast' ).elementToElement( {
            model: 'chartEmbedElement',
            view: ( modelElement, { writer } ) => {
                return writer.createEmptyElement( 'span', {
                    'indicator-id': modelElement.getAttribute( 'indicator-id' )
                } );
            }
        } );

        editor.conversion.for( 'editingDowncast' ).elementToElement( {
            model: 'chartEmbedElement',
            view: ( modelElement, { writer } ) => {

                const externalDataPreviewElement = writer.createRawElement( 'div', {id: 'chart', 'indicator-id': modelElement.getAttribute('indicator-id')}, function( domElement ) {
                    //domElement.classList.add( 'external-data-widget' );

                } );

                const externalWidgetContainer = writer.createContainerElement( 'div', {class: '', 'x-data': '{}', 'x-init': "new PlotlyChart('chart')"}, externalDataPreviewElement );

                return toWidget( externalWidgetContainer, writer, {
                    label: 'External widget'
                } );
            }
        } );
    }
}
