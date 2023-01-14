import type { FormikHelpers } from 'formik';
import { Form, Formik } from 'formik';
import { useState } from 'react';
import tw from 'twin.macro';
import { object, string, number } from 'yup';

import createDomain from '@/api/admin/domains/createDomain';
import ServerSelect from '@/components/admin/domains/ServerSelect';
import getDomains from '@/api/admin/domains/getDomains';
import { Button } from '@/components/elements/button/index';
import { Size, Variant } from '@/components/elements/button/types';
import Field from '@/components/elements/Field';
import Modal from '@/components/elements/Modal';
import FlashMessageRender from '@/components/FlashMessageRender';
import useFlash from '@/plugins/useFlash';

interface Values {
    domain: string;
    server_id: number | null;
}

const schema = object().shape({
    domain: string()
    .required("A subdomain must be provided.")
    .min(6,"Subdomain must be longer then 6 characters.")
    .max(255,"Subdomain must be less then 255 characters"),
    server_id: number().notRequired().nullable().integer("Server id must be a integer.").positive("Must be a positive number.")
});

export default function NewDomainButton(){
    const [visible, setVisible] = useState(false);
    const { clearFlashes, clearAndAddHttpError, addFlash } = useFlash();
    const { mutate } = getDomains(["server"]);

    const submit = ({ domain, server_id }: Values, { setSubmitting }: FormikHelpers<Values>) => {
        clearFlashes('domain:create');
        setSubmitting(true);

        createDomain(domain, server_id)
            .then(async domain => {
                await mutate(data => ({ ...data!, items: data!.items.concat(domain) }), false);
                setVisible(false);
                addFlash({ key: "domains", message: `The subdomain (${domain.domain}) will be ready in 2-5 minutes.`, type: "success" });
            })
            .catch(error => {
                clearAndAddHttpError({ key: 'domain:create', error });
                setSubmitting(false);
            });
    };

    return (
        <>
            <Formik onSubmit={submit} initialValues={{ domain: '', server_id: null } as Values} validationSchema={schema}>
                {({ isSubmitting, resetForm }) => (
                    <Modal
                        visible={visible}
                        dismissable={!isSubmitting}
                        showSpinnerOverlay={isSubmitting}
                        onDismissed={() => {
                            resetForm();
                            setVisible(false);
                        }}
                    >
                        <FlashMessageRender byKey={'domain:create'} css={tw`mb-6`} />

                        <h2 css={tw`mb-6 text-2xl text-neutral-100`}>New Domain</h2>

                        <Form css={tw`m-0`}>
                            <Field
                                type={'text'}
                                id={'domain'}
                                name={'domain'}
                                label={'Domain'}
                                placeholder="example"
                                description={'The subdomain that is to be registered.'}
                                autoFocus
                            />

                            <div css={tw`mt-6`}>
                                <ServerSelect selected={null}/>
                            </div>

                            <div css={tw`flex flex-wrap justify-end mt-6`}>
                                <Button.Text
                                    type="button"
                                    variant={Variant.Secondary}
                                    css={tw`w-full sm:w-auto sm:mr-2`}
                                    onClick={() => setVisible(false)}
                                >
                                    Cancel
                                </Button.Text>
                                <Button css={tw`w-full mt-4 sm:w-auto sm:mt-0`} type={'submit'}>
                                    Create Domain
                                </Button>
                            </div>
                        </Form>
                    </Modal>
                )}
            </Formik>

            <Button
                type="button"
                size={Size.Large}
                css={tw`h-10 px-4 py-0 whitespace-nowrap`}
                onClick={() => setVisible(true)}
            >
                New Domain
            </Button>
        </>
    );
}