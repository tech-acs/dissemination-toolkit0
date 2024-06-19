<x-app-layout>
    <div class="container mx-auto py-12">

        <div class="col-span-3 bg-white" x-cloak x-data="storyEditor()">
            <div id="story-editor"></div>
        </div>

    </div>



    {{--<div>
        <div class="grid grid-cols-3 border border-dashed border-black m-2 bg-green-100 gap-2 p-2">

            <div class="col-span-3 bg-white" x-cloak x-data="storyEditor()">
                <div id="story-editor"></div>
            </div>
        </div>
    </div>--}}

    <script>
        function storyEditor() {
            return {
                init() {
                    ClassicEditor
                        // Note that you do not have to specify the plugin and toolbar configuration — using defaults from the build.
                        .create( document.querySelector( '#story-editor' ) )
                        .then( editor => {
                            console.log( 'Editor was initialized', editor );
                        } )
                        .catch( error => {
                            console.error( error.stack );
                        } );
                }
            }
        }
    </script>
</x-app-layout>
