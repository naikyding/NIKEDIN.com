<template >
  <div class="eventMail">
    <!-- contactUs -->
    <form id="eventMailFrom" method="post" @submit.stop.prevent="postEventMail">
      <section class="contactUs px-1 px-md-5 my-5 d-flex align-items-center">
        <transition name="fade">
          <div v-if="state" class="container px-md-5 inputSpace">
            <h2 class="text-center text-light">THANKS FOR SIGNING UP.</h2>
            <p class="text-center text-light">You have been added to our mailing list.</p>
          </div>
        </transition>

        <div v-if="!state" class="container px-md-5 inputSpace">
          <div class="row text-white px-md-5 inputContent">
            <div class="col-12 col-md-6 text-left"><h1>TAKE 10% OFF, ON US</h1></div>
            <div class="col-12 col-md-6 text-left"><p>Sign up now to enjoy 10% off your purchase and receive the latest news on new arrivals, exclusive promotions, and more.</p></div>
            <div class="col-12 ">
            <div class="input-group mb-3">
              <input type="email" name="eventMail" class="form-control" placeholder="Enter your email" >
              <div class="input-group-append">
                <b-button class="btn contactUsBtn" type="submit" >SUBMIT</b-button>
              </div>
            </div>
            </div>
          </div>
        </div>
      </section>
    </form>
  </div>
</template>

<script>
import mysql from '../admin/mysql.js'

export default {
  name: 'EventMail',
  data: () => ({
  }),
  computed: {
    state () {
      return this.$store.state.eventMail.state
    }
  },
  mounted () {
  },
  methods: {
    postEventMail () {
      const data = new FormData(document.querySelector('#eventMailFrom'))
      mysql.post('POST_eventMail', data).then((res) => {
        if (res) {
          this.$store.commit('eventMailEv', true)
        }
      })
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
h3 {
  margin: 40px 0 0;
}
ul {
  list-style-type: none;
  padding: 0;
}
li {
  display: inline-block;
  margin: 0 10px;
}
a {
  color: #42b983;
}
.contactUs{
  height:350px;
  width:100;
  background:
    linear-gradient(to right, rgba(0,0,0,.5), rgba(202, 67, 67, 0.1)),
    url('../assets/ck_bg.jpg') no-repeat center top fixed ;
  background-size: cover;
  border-bottom: 7px solid #C79C57;
  text-shadow: 0 0 1px #999;
}
.contactUsBtn {
  background-color: #C79C57;
  color: #fff;
  border: 1px solid #C79C57;
}
.contactUsBtn:hover{
  color: #000;
}

.fade-enter-active, .fade-leave-active {
  transition: opacity .5s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
  opacity: 0;
}
</style>
