import Modal from 'flarum/common/components/Modal';
import Button from 'flarum/common/components/Button';
import app from 'flarum/app';

export default class ShowCodeModal extends Modal {
    className() {
        return 'Modal--small';
    }

    title() {
        return app.translator.trans('shawplusroot-shop.lib.modal.purchase_detail');
    }

    content() {
        const invitationCode = this.attrs.invitationCode;

        return [
            m('div', { className: 'Modal-body' }, [
                m('p', 
                app.translator.trans('shawplusroot-shop.lib.modal.instruction',{
                    invitationCode: invitationCode,
                })),
                m('.Form-group', Button.component({
                    type: 'submit',
                    className: 'Button Button--primary',
                    loading: this.loading
                }, 
                    app.translator.trans('shawplusroot-shop.lib.modal.confirm')
                ))
            ]),
        ];
    }

    onsubmit(event: Event) {
        event.preventDefault();
        this.hide();
        this.loading = false;
        location.reload();
    }
}
