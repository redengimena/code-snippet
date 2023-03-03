<script>
import MediaModal from '../mixins/modal'

export default {
    name: 'vizzy-image-field',
    mixins: [MediaModal],    
    data() {
        return {
            cover: ''
        }
    },
    mounted() {
        EventHub.listen('file_selected', (path) => {
            if (this.$refs.input) {
                this.$refs.input.dispatchEvent(new Event("change"));
            }
        })
    },    
}
</script>