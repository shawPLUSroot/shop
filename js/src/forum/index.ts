import { extend } from 'flarum/extend';
import app from 'flarum/app';
import UserCard from "flarum/components/UserCard";
import Model from "flarum/Model";
import User from "flarum/models/User";
import ConfirmPurchaseModal from './ConfirmPurchaseModal';
import Button from 'flarum/common/components/Button';
import PurchaseHistoryPage from './PurchaseHistoryPage';
import UserPage from 'flarum/components/UserPage';

app.initializers.add('shawplusroot/shop', () => {

  User.prototype.purchase = Model.attribute("purchase");

  // app.routes.userPurchaseHistory = {
  //   path: '/u/:username/purchase',
  //   component: PurchaseHistoryPage,
  // };

  // extend(UserPage.prototype, 'navItems', function (items) {
  //   if (app.session.user === this.attrs.user) {
  //       items.add('purchase-history', LinkButton.component({
  //         href: app.route('userPurchaseHistory', {
  //             username: this.user.slug(),
  //         }),
  //         icon: 'fas fa-receipt',
  //     }, 'Purchase-History'));
  //   }
  // }); 

  extend(UserCard.prototype, "infoItems", function (items) {
      items.add("purchase", m('p', this.attrs.user.purchase()));
      if (app.session.user === this.attrs.user) {
          items.add("shop", m('.Form-group', Button.component({
            className: 'Button',
            onclick() {
                app.modal.show(ConfirmPurchaseModal);
            },
          },   app.translator.trans('shawplusroot-shop.forum.purchase'))));
      }
  });



});
