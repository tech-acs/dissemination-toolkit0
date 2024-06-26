<x-app-layout>
    <div class="container mx-auto py-12">

        <div class="col-span-3 bg-white" x-cloak x-data="storyEditor()">

            <div class="col-span-3 bg-gray-50 py-2">
                <x-button x-on:click="save()">Save</x-button>
            </div>

            <div id="story-editor"></div>

        </div>

    </div>

    <script>
        function storyEditor() {
            return {
                editor: null,
                storyId: @json($story->id),
                storyHtml: @json($story->html),

                init() {
                    ClassicEditor
                        // Note that you do not have to specify the plugin and toolbar configuration — using defaults from the build.
                        .create(document.querySelector('#story-editor'))
                        .then(e => {
                            this.editor = e
                            e.setData(this.storyHtml)
                        })
                        .catch( error => {
                            console.error( error.stack )
                        });
                },

                async save() {
                    let data = Alpine.raw(this.editor).getData()
                    const response = await axios.patch(`/manage/story-builder/${this.storyId}`, { storyHtml: data }, {validateStatus: () => true})
                    console.log(data, 'Save response: (response.status, response.data)', response.status, response.data)
                    if (response.status === 200) {
                        this.$dispatch('notify', {type: 'success', content: 'Successfully saved'})
                    } else {
                        this.$dispatch('notify', {type: 'error', content: 'Error while saving'})
                    }
                }
            }
        }
    </script>
    <x-toast />
</x-app-layout>
