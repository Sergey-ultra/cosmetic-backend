import { createStore } from 'vuex'
import brand from './brand'
import country from './country'
import image from './image'
import auth from './auth'
import notification from './notification'
import category from './category'
import ingredient from './ingredient'
import sku from './sku'
import user from './user'
import parser from './parser'
import parserOneBatch from './parserOneBatch'
import store from './store'
import review from './review'
import dynamics from './dynamics'
import article from './article'
import productParser from './product-parser'
import priceParser from './price-parser'
import linkParser from './link-parser'
import reviewParser from './review-parser'
import parsingLink from "./parsing-link";
import productOptions from './product-options'
import priceOptions from './price-options'
import linkOptions from './link-options'
import comment from './comment'
import question from './question'
import supplier from './supplier'
import tag from './tag'
import articleComment from './article-comment'
import video from './video'
import settings from './settings'
import message from './message'





export default createStore({
    modules: {
        auth,
        brand,
        category,
        country,
        image,
        notification,
        ingredient,
        sku,
        user,
        parser,
        parserOneBatch,
        store,
        review,
        dynamics,
        article,
        productParser,
        priceParser,
        linkParser,
        reviewParser,
        parsingLink,
        productOptions,
        priceOptions,
        linkOptions,
        comment,
        question,
        supplier,
        tag,
        articleComment,
        video,
        settings,
        message,
    }
})


