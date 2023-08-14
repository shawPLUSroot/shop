import Modal from 'flarum/common/components/Modal';
import Button from 'flarum/common/components/Button';
import app from 'flarum/app';
import ShowCodeModal from './ShowCodeModal';

export default class ConfirmPurchaseModal extends Modal {

    amount = '';
    lastActivity = '';

    className() {
        return 'Modal--small';
    }

    title() {
        return 'Confirm Purchase';
    }


    content() {
        return m('.Modal-body', [
            m('.Form-group', [
                m('label', 
                    app.translator.trans('shawplusroot-shop.lib.modal.invite_code_amount')
                ),
                m('input.FormControl', {
                    type: 'number',
                    value: this.amount,
                    onchange: (event: InputEvent) => {
                        this.amount = (event.target as HTMLInputElement).value;
                    },
                    min: 0,
                    step: 1,
                    max: 100,
                    disabled: this.loading,
                })
            ]),
      
            m('.Form-group', Button.component({
                type: 'submit',
                className: 'Button Button--primary',
                loading: this.loading,
                disabled: parseFloat(this.amount || '0') <= 0,
            }, 
                app.translator.trans('shawplusroot-shop.lib.modal.confirm')
            ))
        ]);
    }

    request(dryRun: boolean = false) {
        return app.request<{ userMatchCount: number }>({
            method: 'POST',
            url: app.forum.attribute('apiUrl') + '/purchase',
            errorHandler: this.onerror.bind(this),
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Token ${app.session.token}`
            },
            body: {
                amount: this.amount,
                dryRun,
            },
        });
    }

    onsubmit(event: Event) {
        event.preventDefault();

        this.loading = true;

        this.request()
        .then((payload) => {
          if (payload.success) {
            return this.requestInviteCode();
          } else {
            app.alerts.show({type: 'error'}, app.translator.trans('shawplusroot-shop.lib.modal.insufficient_money_alert'));
            throw new Error('Insufficient money');
          }
        })
        .then((response) => {
          app.modal.show(ShowCodeModal, { invitationCode: response.data.attributes.key });
        })
        .catch((error) => {
          this.loading = false;
          m.redraw();
        });
      
    }


    requestInviteCode(dryRun: boolean = false) {
        const key = Math.random().toString(36).substring(2, 10);
        return app.request({
          method: 'POST',
          url: app.forum.attribute('apiUrl') + '/getcustomcode',
          errorHandler: this.onerror.bind(this),
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Token ${app.session.token}`
          },
          body: {
            data:{
                attributes: {
                    key: key,
                    groupId: 3,
                    maxUses: this.amount,
                    activates: true
                  }
            }
            
          },
        });
      }
      

}


