import { ClassicEditor as ClassicEditorBase } from '@ckeditor/ckeditor5-editor-classic';
import { Essentials } from '@ckeditor/ckeditor5-essentials';
import { Autoformat } from '@ckeditor/ckeditor5-autoformat';
import { Bold, Italic } from '@ckeditor/ckeditor5-basic-styles';
import { BlockQuote } from '@ckeditor/ckeditor5-block-quote';
import { Heading } from '@ckeditor/ckeditor5-heading';
import { Link } from '@ckeditor/ckeditor5-link';
import { List } from '@ckeditor/ckeditor5-list';
import { Paragraph } from '@ckeditor/ckeditor5-paragraph';
import {Table, TableColumnResize, TableToolbar} from "@ckeditor/ckeditor5-table";
import {MediaEmbed} from "@ckeditor/ckeditor5-media-embed";
import {Indent, IndentBlock} from "@ckeditor/ckeditor5-indent";
import {Image, ImageCaption, ImageInsert, ImageResize, ImageUpload} from '@ckeditor/ckeditor5-image';
import {HtmlEmbed} from '@ckeditor/ckeditor5-html-embed';

import ChartEmbed from "./ChartEmbed/ChartEmbed";

export default class ClassicEditor extends ClassicEditorBase {}

ClassicEditor.builtinPlugins = [
    Essentials,
    Autoformat,
    Bold,
    Italic,
    BlockQuote,
    Heading,
    Link,
    List,
    Paragraph,
    Table, TableToolbar, TableColumnResize, MediaEmbed, Indent, IndentBlock, Image, ImageCaption, ImageInsert, ImageResize, ImageUpload,
    HtmlEmbed,
    ChartEmbed
];

ClassicEditor.defaultConfig = {
    toolbar: {
        items: [
            'undo', 'redo',
            '|', 'heading',
            '|', 'bold', 'italic',
            '|', 'link', 'insertImage', 'insertTable', 'mediaEmbed', 'blockQuote',
            '|', 'bulletedList', 'numberedList', 'outdent', 'indent',
            '|', 'htmlEmbed',
            '|', 'chartEmbed'
        ]
    },
    language: 'en',
    table: {
        contentToolbar: [ 'tableColumn', 'tableRow', 'mergeTableCells' ]
    },
    htmlEmbed: {
        showPreviews: true,
        sanitizeHtml: ( inputHtml ) => {
            // Strip unsafe elements and attributes, for example:
            // the `<script>` elements and `on*` attributes.
            //const outputHtml = sanitize( inputHtml );

            return {
                html: inputHtml,
                // true or false depending on whether the sanitizer stripped anything.
                hasChanged: false
            };
        }
    }
};

