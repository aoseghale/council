<template>
    <div id="'reply-'+id" class="panel" :class="isBest ? 'panel-success' : 'panel-default'">
        <div class="panel-heading">
            <div class="level">
                <h5 class="flex">
                    <a :href="'/profiles/'+reply.owner.name"
                        v-text="reply.owner.name">
                    </a> said <span v-text="ago"></span>
                </h5>

                <div v-if="signedIn">
                    <favorite :reply="reply"></favorite>
                </div>
            </div>
        </div>

        <div class="panel-body">
            <div v-if="editing">
                <form @submit="update" action="">
                    <div class="form-group">
                        <wysiwyg v-model="body"></wysiwyg>
                        <!--<textarea class="form-control" v-model="body" required></textarea>-->
                    </div>

                    <button class="btn btn-xs btn-primary">Update</button>
                    <button class="btn btn-xs btn-link" @click="editing = false" type="button">Cancel</button>
                </form>
            </div>

            <div v-else>
                <highlight :content="body"></highlight>
            </div>
        </div>

        <div class="panel-footer level" v-if="authorize('owns', reply) || authorize('owns', reply.thread)">
            <div v-if="authorize('owns', reply)">
                <button class="btn btn-xs mr-1" @click="editing = true">Edit</button>
                <button class="btn btn-xs btn-danger mr-1" @click="destroy">Delete</button>
            </div>

            <button class="btn btn-xs btn-default ml-a" @click="markBestReply" v-if="authorize('owns', reply.thread)">Best Reply?</button>
        </div>
    </div>
</template>

<script>
    import Favorite from "./Favorite.vue";
    import Highlight from "./Highlight.vue";
    import moment from 'moment';

    export default {
        props: ['reply'],

        components: { Favorite, Highlight },

        data() {
            return {
                editing: false,
                id: this.reply.id,
                body: this.reply.body,
                isBest: this.reply.isBest,
            }
        },

        computed: {
            ago() {
                return moment(this.reply.created_at).fromNow() + '...';
            }
        },

        created () {
            window.events.$on('best-reply-selected', id => {
                this.isBest = (id === this.id);
            });
        },

        // mounted() {
        //     this.highlight(this.$refs['body']);
        // },

        // watch: {
        //     editing() {
        //         if (this.editing) return;
        //
        //         this.$nextTick(() => {
        //             this.highlight(this.$refs['body']);
        //         });
        //     }
        // },

        methods: {
            update() {
                axios.patch(
                    '/replies/' + this.id,  {
                        body: this.body
                    })
                    .catch(error => {
                        flash(error.response.data, 'danger');
                    });

                this.editing = false;

                flash('Updated!');
            },

            destroy() {
                axios.delete('/replies/' + this.id);

                // Using emit event instead of fadeout code below
                this.$emit('deleted', this.id);

//                $(this.$el).fadeOut(300, () => {
//                    flash('Your reply has been deleted');
//                });
            },

            markBestReply() {
                axios.post('/replies/' + this.id + '/best');

                window.events.$emit('best-reply-selected', this.id);
            }
        }
    }
</script>