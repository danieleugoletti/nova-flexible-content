<template>
    <div>
        <form-file-field
                :field="field"
                :resource-name="resourceName"
                :resource-id="resourceId"
                :resource="resource"
                :errors="errors"
                ref="formFileField"
            >
        </form-file-field>
    </div>
</template>

<script>

export default {
    props: [
        'field',
        'errors',
        'resourceId',
        'resourceName',
        'resource',
    ],

    mounted() {
        this.$refs.formFileField.removeFile = () => {
            this.$refs.formFileField.closeRemoveModal()
            this.$refs.formFileField.deleted = true
            this.field.value = ''
            this.$emit('file-deleted')
        };
    }
}
</script>
