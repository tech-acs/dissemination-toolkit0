import { ClassicEditor as ClassicEditorBase } from '@ckeditor/ckeditor5-editor-classic';
import { Essentials } from '@ckeditor/ckeditor5-essentials';
import { Autoformat } from '@ckeditor/ckeditor5-autoformat';
import { Bold, Italic, Underline } from '@ckeditor/ckeditor5-basic-styles';
import { BlockQuote } from '@ckeditor/ckeditor5-block-quote';
import { Heading } from '@ckeditor/ckeditor5-heading';
import { Link, LinkImage } from '@ckeditor/ckeditor5-link';
import { List } from '@ckeditor/ckeditor5-list';
import { Paragraph } from '@ckeditor/ckeditor5-paragraph';
import { Table, TableColumnResize, TableToolbar } from "@ckeditor/ckeditor5-table";
import { MediaEmbed } from "@ckeditor/ckeditor5-media-embed";
import { Indent, IndentBlock } from "@ckeditor/ckeditor5-indent";
import {
    Image,
    ImageCaption, ImageInline,
    ImageInsert,
    ImageResize,
    ImageResizeEditing, ImageResizeHandles, ImageStyle, ImageToolbar,
    ImageUpload
} from '@ckeditor/ckeditor5-image';
import { HorizontalLine } from "@ckeditor/ckeditor5-horizontal-line";
import { Base64UploadAdapter } from "@ckeditor/ckeditor5-upload";
import { RemoveFormat } from "@ckeditor/ckeditor5-remove-format";
import { HtmlEmbed } from '@ckeditor/ckeditor5-html-embed';
import { SourceEditing } from "@ckeditor/ckeditor5-source-editing";
import { TextTransformation } from "@ckeditor/ckeditor5-typing";
import { Font } from "@ckeditor/ckeditor5-font";
import { ShowBlocks } from "@ckeditor/ckeditor5-show-blocks";
import { Style } from "@ckeditor/ckeditor5-style";
import { GeneralHtmlSupport } from "@ckeditor/ckeditor5-html-support";
import { Alignment } from "@ckeditor/ckeditor5-alignment";

import ChartEmbed from "./ChartEmbed/ChartEmbed";

export default class ClassicEditor extends ClassicEditorBase {}

ClassicEditor.builtinPlugins = [
    Essentials, Autoformat, Font, Style, GeneralHtmlSupport, Bold, ShowBlocks, Alignment,
    Italic, Underline, BlockQuote, Heading, TextTransformation, Link, List, Paragraph,
    Table, TableToolbar, TableColumnResize, MediaEmbed, Indent, IndentBlock,
    Image, ImageCaption, ImageStyle, ImageInsert, ImageResize, ImageUpload, ImageToolbar, LinkImage, ImageInline,
    ImageResizeEditing, ImageResizeHandles,
    HtmlEmbed, SourceEditing, HorizontalLine, Base64UploadAdapter, RemoveFormat,
    ChartEmbed
];

ClassicEditor.defaultConfig = {
    toolbar: {
        items: [
            'undo', 'redo',
            '|', 'heading', 'style',
            '|', 'link', 'insertImage', 'insertTable', 'blockQuote', 'mediaEmbed', 'htmlEmbed', 'horizontalLine',
            '|', 'bold', 'italic', 'underline',
            {
                label: 'Basic styles',
                icon: 'text',
                items: [
                    'fontSize',
                    'fontFamily',
                    'fontColor',
                    'fontBackgroundColor'
                ],
            },
            'removeFormat',
            '|', 'alignment',
            '|', 'bulletedList', 'numberedList',
            '|', 'outdent', 'indent',
            '|', 'showBlocks', 'sourceEditing',
            '|', 'chartEmbed',
        ], shouldNotGroupWhenFull: true,
    },
    language: 'en',
    table: {
        contentToolbar: [ 'tableColumn', 'tableRow', 'mergeTableCells' ]
    },
    alignment: {
        options: [ 'left', 'right' ]
    },
    style: {
        definitions: [
            {
                name: 'Article category',
                element: 'h3',
                classes: [ 'category' ]
            },
            {
                name: 'Info box',
                element: 'p',
                classes: [ 'info-box' ]
            },
        ]
    },
    // ToDo: define the styles.
    image: {
        toolbar: [
            'imageStyle:block',
            'imageStyle:alignBlockLeft',
            'imageStyle:alignBlockRight',
            '|',
            'imageStyle:inline',
            'imageStyle:alignLeft',
            'imageStyle:alignRight',
            '|',
            'toggleImageCaption',
            'imageTextAlternative',
            '|',
            'linkImage'
        ],
        insert: {
            // If this setting is omitted, the editor defaults to 'block'.
            // See explanation below.
            type: 'auto'
        }
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

