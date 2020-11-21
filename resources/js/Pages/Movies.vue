<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Manage Movies
      </h2>
    </template>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg px-4 py-4">
          <div class="bg-teal-100 border-t-4 border-teal-500 rounded-b
              text-teal-900 px-4 py-3 shadow-md my-3" role="alert"
               v-if="$page.flash.message">
            <div class="flex">
              <div>
                <p class="text-sm">{{ $page.flash.message }}</p>
              </div>
            </div>
          </div>
          <button @click="openModal()" class="bg-blue-500 hover:bg-blue-700 text-white
              font-bold py-2 px-4 rounded my-3">
            New Movie
          </button>

          <FormSection v-for="movie in movies" :key="movie.id">
            <template #title>
              {{ movie.title }}
            </template>

            <template #description>
              {{ movie.description }}
            </template>

            <template #form>
              <div class="col-span-6 sm:col-span-4">
                <p>Rating: {{ movie.average_stars ? movie.average_stars : 0 }} / 5.0</p>
                <p>Reviews count: {{ movie.reviews_count }}</p>
              </div>
              <div class="col-span-6 sm:col-span-4">
                <div class="ml-12" v-for="review in movie.reviews">
                  <div class="mt-2 text-sm text-gray-500">
                    {{ review.stars + " - " + review.note }}
                  </div>
                </div>
              </div>
            </template>

            <template #actions>
              <JetButton @click.native="edit(movie)" class="mr-2">
                Edit
              </JetButton>
              <JetButton @click.native="deleteMovie(movie)" class="mr-2">
                Delete
              </JetButton>
              <JetButton @click.native="openReviewModal(movie)">
                Rate
              </JetButton>
            </template>
          </FormSection>

          <DialogModal :show="isOpen" @close="closeModal">
            <template #title>{{ editMode ? "Edit" : "New" }} Movie</template>

            <template #content>
              <JetInput type="text"
                        v-model="form.title"
                        @keyup.enter.native="submitForm(form)" />

              <div class="mt-2">
                <label for="exampleFormControlInput2" class="block text-gray-700 text-sm font-bold mb-2">
                  Description:
                </label>
                <textarea class="shadow appearance-none border rounded w-full py-2 px-3
                              text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                          id="exampleFormControlInput2"
                          v-model="form.description"
                          placeholder="Enter Description" />
              </div>

              <div class="mb-4">
                <JetInputError :message="form.error" class="mt-2" />
              </div>
            </template>

            <template #footer>
              <jet-secondary-button @click.native="closeModal">
                Cancel
              </jet-secondary-button>

              <jet-button class="ml-2" @click.native="submitForm(form)"
                          :class="{ 'opacity-25': form.processing }"
                          :disabled="form.processing">
                Save
              </jet-button>
            </template>
          </DialogModal>

          <DialogModal :show="reviewModalOpen" @close="closeReviewForm">
            <template #title>Review form</template>

            <template #content>
              <label for="stars" class="block text-gray-700 text-sm font-bold mb-2">Stars: </label>
              <JetInput type="number"
                        id="stars"
                        min="0"
                        max="5"
                        v-model="reviewForm.stars"/>

              <div class="mt-2">
                <label for="exampleFormControlInput2" class="block text-gray-700 text-sm font-bold mb-2">
                  Review:
                </label>
                <textarea class="shadow appearance-none border rounded w-full py-2 px-3
                              text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                          id="exampleFormControlInput2"
                          v-model="reviewForm.note"
                          placeholder="Enter Review Text" />
              </div>

              <div class="mb-4">
                <JetInputError :message="reviewForm.error" class="mt-2" />
              </div>
            </template>
            <template #footer>
              <jet-secondary-button @click.native="closeReviewForm">
                Cancel
              </jet-secondary-button>

              <jet-button class="ml-2" @click.native="submitReviewForm(reviewForm)"
                          :class="{ 'opacity-25': reviewForm.processing }"
                          :disabled="reviewForm.processing">
                Save
              </jet-button>
            </template>
          </DialogModal>
        </div>
      </div>
    </div>
  </app-layout>
</template>
<script>
  import AppLayout from '../Layouts/AppLayout'
  import Welcome from '../Jetstream/Welcome'
  import DialogModal from '../Jetstream/DialogModal';
  import JetButton from '../Jetstream/Button'
  import JetInput from '../Jetstream/Input'
  import JetInputError from '../Jetstream/InputError'
  import JetSecondaryButton from '../Jetstream/SecondaryButton'
  import FormSection from '../Jetstream/FormSection';

  export default {
    components: {
      DialogModal,
      JetInput,
      JetInputError,
      JetButton,
      JetSecondaryButton,
      FormSection,
      AppLayout,
      Welcome,
    },
    props: ['movies', 'errors'],
    data() {
      return {
        editMode: false,
        isOpen: false,
        reviewModalOpen: false,
        form: {
          title: null,
          description: null,
          error: ''
        },
        reviewForm: {
          reviewable_id: null,
          note: '',
          stars: 0,
          error: '',
        }
      }
    },
    created() {
      Echo.channel('movies')
        .listen('.movie.created', (e) => {
          this.flashMessage.info({
            title: 'New Movie Created',
            message: `New Movie named '${e.movie.title}' was crated!`
          });
          this.reloadData();
        })
        .listen('.movie.updated', (e) => {
          this.flashMessage.info({
            title: 'Movie Updated',
            message: `Movie named '${e.movie.title}' was updated.`
          });
          this.reloadData();
        })
        .listen('.movie.deleted', () => {
          this.reloadData();
        });
    },
    destroyed() {
      Echo.leaveChannel('movies');
    },
    methods: {
      reloadData: function () {
        this.$inertia.get('/movies');
      },
      openModal: function () {
        this.isOpen = true;
      },
      closeModal: function () {
        this.isOpen = false;
        this.reset();
        this.editMode=false;
      },
      reset: function () {
        this.form = {
          title: null,
          description: null,
          error: ''
        }
      },
      openReviewModal(movie) {
        this.reviewForm.reviewable_id = movie.id;
        this.reviewModalOpen = true;
      },
      closeReviewForm() {
        this.reviewModalOpen = false;
        this.resetReviewForm();
      },
      resetReviewForm() {
        this.reviewForm = {
          note: '',
          stars: 0,
          error: '',
          reviewable_id: null,
        }
      },
      submitReviewForm (review) {
        this.$inertia.post('/reviews', review)
          .then(() => {
            this.resetReviewForm();
            this.closeReviewForm();
          })
          .catch(() => {
            this.resetReviewForm();
            this.closeReviewForm();
          });
      },
      submitForm: function (data) {
        this.editMode ? this.update(data) : this.save(data)
      },
      save: function (data) {
        this.$inertia.post('/movies', data)
        this.reset();
        this.closeModal();
        this.editMode = false;
      },
      edit: function (data) {
        this.form = Object.assign({}, data);
        this.editMode = true;
        this.openModal();
      },
      update: function (data) {
        data._method = 'PUT';
        this.$inertia.post('/movies/' + data.id, data)
        this.reset();
        this.closeModal();
      },
      deleteMovie: function (data) {
        if (!confirm('Are you sure want to remove?')) return;
        data._method = 'DELETE';
        this.$inertia.post('/movies/' + data.id, data)
        this.reset();
        this.closeModal();
      }
    }
  }
</script>