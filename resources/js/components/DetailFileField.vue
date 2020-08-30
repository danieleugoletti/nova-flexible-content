<template>
    <div>
        <detail-file-field
                :field="modifiedField"
                :resource-name="resourceName"
                :resource-id="resourceId"
                :resource="resource"
            >
        </detail-file-field>

        <p v-if="shouldShowToolbar" class="flex items-center text-sm mt-3">
            <a
            v-if="field.downloadable"
            :dusk="field.attribute + '-download-link'"
            @keydown.enter.prevent="download"
            @click.prevent="download"
            tabindex="0"
            class="cursor-pointer dim btn btn-link text-primary inline-flex items-center"
            >
            <icon
                class="mr-2"
                type="download"
                view-box="0 0 24 24"
                width="16"
                height="16"
            />
            <span class="class mt-1">{{ __('Download') }}</span>
            </a>
        </p>
    </div>
</template>

<script>
export default {
    props: ['resource', 'resourceName', 'resourceId', 'field', 'errors'],

    methods: {
        download() {
            const { resourceName, resourceId } = this
            const attribute = this.field.attribute

            let link = document.createElement('a')
            link.href = `/nova-flexible-content/${resourceName}/${resourceId}/download/${attribute}`
            link.download = 'download'
            document.body.appendChild(link)
            link.click()
            document.body.removeChild(link)
        },
    },

    computed: {
        hasValue() {
            return (
                Boolean(this.field.value || this.imageUrl)
            )
        },

        imageUrl() {
            return this.field.previewUrl || this.field.thumbnailUrl
        },

        shouldShowToolbar() {
            return Boolean(this.field.downloadable && this.hasValue)
        },

        modifiedField() {
            let newField = Object.assign({}, this.field);
            newField.downloadable = false;
            return newField;
        }
    },
}
</script>
