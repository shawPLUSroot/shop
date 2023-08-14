import app from 'flarum/forum/app';
import UserPage from 'flarum/components/UserPage';
import {ApiPayloadPlural} from 'flarum/common/Store';


export default class RewardHistoryPage extends UserPage {
    loading: boolean = true
    rewards: Reward[] = []

    oninit(vnode: any) {
        super.oninit(vnode);

        this.loadUser(m.route.param('username'));
    }

    show(user: any) {
        super.show(user);

        app.setTitle('title');

        this.loadRewards();
    }

    loadRewards() {
        app.request<ApiPayloadPlural>({
            method: 'GET',
            url: app.forum.attribute('apiUrl') + '/users/' + this.user!.id() + '/purchase',
        }).then(payload => {
            this.rewards = app.store.pushPayload<Record[]>(payload);
            this.loading = false;
            m.redraw();
        });
    }

    content() {
        // if (this.loading) {
        //     return LoadingIndicator.component();
        // }

        // return 
    }
}