import NumberFlow, { type Format } from "@number-flow/react";
import { resolveCurrencyFormat } from "@sumup/intl";
import { useReducedMotion } from "motion/react";
import * as m from "motion/react-m";
import * as React from "react";
import ArrowRight01Icon from "virtual:icons/hugeicons/arrow-right-01";
import CheckmarkCircle02SolidIcon from "virtual:icons/hugeicons/checkmark-circle-02-solid";
import CreditCardIcon from "virtual:icons/hugeicons/credit-card";

import { useTranslation } from "#/hooks/use-translation.ts";
import { useUser } from "#/hooks/use-user.ts";
import { routes } from "#/routes.ts";
import { cn } from "#/utils/cn.ts";
import { type Plan, plans } from "#/utils/plans.ts";
import { purify } from "#/utils/sanitize.ts";
import { Link } from "../link.tsx";
import * as Button from "../ui/button.tsx";
import * as SegmentedControl from "../ui/segmented-control.tsx";

type PricingSectionProps = React.ComponentPropsWithRef<"section"> & {
    containerClassName?: string;
    isInternal?: boolean;
};

export function PricingSection({ containerClassName, isInternal, ...props }: PricingSectionProps) {
    const [interval, setInterval] = React.useState("monthly");
    const { language, t } = useTranslation();
    const isReducedMotion = useReducedMotion();
    const currencyFormat = resolveCurrencyFormat(language, "USD");

    const format: Format = React.useMemo(
        () => ({
            style: "currency",
            currency: "USD",
            minimumFractionDigits: currencyFormat?.minimumFractionDigits,
            maximumFractionDigits: currencyFormat?.maximumFractionDigits,
        }),
        [currencyFormat?.maximumFractionDigits, currencyFormat?.minimumFractionDigits],
    );

    return (
        <section id="pricing" {...props}>
            <div className={cn("container border-x border-t bg-(--bg-white-0) py-12 lg:px-12", containerClassName)}>
                {!isInternal && (
                    <div className="grid grid-cols-1 items-end gap-8 lg:grid-cols-3">
                        <div className="lg:col-span-2">
                            <m.p
                                {...(!isReducedMotion && {
                                    animate: { opacity: 1, y: 0 },
                                    initial: { opacity: 0, y: -100 },
                                    transition: { duration: 1 },
                                })}
                                className="flex items-center gap-2"
                            >
                                <CreditCardIcon className="size-4 text-primary" />
                                <span className="text-paragraph-sm font-medium text-(--text-sub-600) uppercase">
                                    pricing
                                </span>
                            </m.p>

                            <m.h3
                                {...(!isReducedMotion && {
                                    animate: { opacity: 1, y: 0 },
                                    initial: { opacity: 0, y: 100 },
                                    transition: { duration: 1 },
                                })}
                                aria-label={t("pricing.simple_pricing")}
                                className="mt-8 text-h4 font-bold tracking-tight"
                            >
                                {t("pricing.simple_pricing")}
                            </m.h3>
                            <m.p
                                {...(!isReducedMotion && {
                                    animate: { opacity: 1, y: 0 },
                                    initial: { opacity: 0, y: 100 },
                                    transition: { duration: 1.5 },
                                })}
                                className="mt-2 text-paragraph-lg text-(--text-sub-600)"
                            >
                                Start with <span className="font-bold underline decoration-wavy">personal</span> plan.
                                Then upgrade as your <strong>financial needs</strong> grow, from individual budgeting to
                                enterprise-scale management.
                            </m.p>
                        </div>

                        <div className="flex lg:ml-auto">
                            <Button.Root asChild className="w-full gap-2 lg:w-auto">
                                <Link href="mailto:sales@expensetrackr.app">
                                    Contact sales
                                    <Button.Icon
                                        as={ArrowRight01Icon}
                                        className="easy-out-in duration-300 group-hover:translate-x-1"
                                    />
                                </Link>
                            </Button.Root>
                        </div>
                    </div>
                )}

                <div className="mx-auto mt-12 flex flex-col items-center">
                    <SegmentedControl.Root
                        aria-label={t("pricing.interval_label")}
                        defaultValue={interval}
                        onValueChange={setInterval}
                    >
                        <SegmentedControl.List
                            className="w-fit gap-2 rounded-full"
                            floatingBgClassName="rounded-full bg-primary"
                        >
                            <SegmentedControl.Trigger
                                className="px-3 text-(--text-strong-950) data-[state=active]:text-white"
                                value="monthly"
                            >
                                {t("common.monthly")}
                            </SegmentedControl.Trigger>
                            <SegmentedControl.Trigger
                                className="px-3 text-(--text-strong-950) data-[state=active]:text-white"
                                value="yearly"
                            >
                                {t("common.yearly")}
                            </SegmentedControl.Trigger>
                        </SegmentedControl.List>
                    </SegmentedControl.Root>

                    <div className="mt-12 grid grid-cols-1 gap-4 lg:grid-cols-3">
                        {plans.map((plan) => {
                            const features = t(`pricing.${plan.code}.features`).split(",");

                            return (
                                <div
                                    className={cn(
                                        "rounded-24 p-8 shadow-sm",
                                        plan.isFeatured
                                            ? "bg-brand-primary-500"
                                            : "bg-(--bg-white-0) ring-1 ring-(--stroke-soft-200)/40 ring-inset",
                                    )}
                                    key={plan.code}
                                >
                                    <div className="block flex-shrink-0">
                                        <div className="flex items-center">
                                            <div
                                                className="flex size-12 shrink-0 items-center justify-center rounded-full bg-(--color-plan-color)/20 shadow-xs ring-(--stroke-soft-200)"
                                                style={{
                                                    "--color-plan-color": plan.iconColor,
                                                }}
                                            >
                                                <plan.icon
                                                    aria-hidden="true"
                                                    className="size-6 text-(--color-plan-color)"
                                                    focusable="false"
                                                />
                                            </div>
                                            <div className="ml-3">
                                                <p
                                                    className={cn(
                                                        "text-paragraph-xs font-medium",
                                                        plan.isFeatured ? "text-white" : "text-(--text-sub-600)",
                                                    )}
                                                >
                                                    {t(`pricing.${plan.code}.target_audience`)}
                                                </p>
                                                <p className={cn("font-medium", plan.isFeatured ? "text-white" : "")}>
                                                    {t(`pricing.${plan.code}.title`)}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="mt-6">
                                        <p
                                            className={cn(
                                                "text-paragraph-sm",
                                                plan.isFeatured ? "text-white" : "text-(--text-sub-600)",
                                            )}
                                        >
                                            {t(`pricing.${plan.code}.description`)}
                                        </p>
                                        <p className="mt-8 h-15 font-semibold tracking-tight">
                                            {plan.code === "enterprise" ? (
                                                <span className="text-h4 font-semibold lg:text-h3">
                                                    {t("pricing.enterprise.custom_price")}
                                                </span>
                                            ) : (
                                                <NumberFlow
                                                    className={cn(
                                                        "text-h4 font-semibold lg:text-h3 [&::part(suffix)]:text-paragraph-xs [&::part(suffix)]:font-normal [&::part(suffix)]:text-(--text-sub-600)",
                                                        plan.isFeatured
                                                            ? "text-white [&::part(suffix)]:text-white"
                                                            : "",
                                                    )}
                                                    format={format}
                                                    suffix={
                                                        !plan.price.onetime
                                                            ? interval === "yearly"
                                                                ? t("pricing.suffix.yearly")
                                                                : t("pricing.suffix.monthly")
                                                            : undefined
                                                    }
                                                    value={
                                                        plan.price[interval as keyof typeof plan.price] ||
                                                        plan.price.onetime ||
                                                        0
                                                    }
                                                />
                                            )}
                                        </p>
                                    </div>

                                    <div className="mt-8">
                                        <PlanButton interval={interval} plan={plan} />
                                    </div>

                                    <ul className="order-last mt-10 flex flex-col gap-y-3" role="list">
                                        {features.map((feature, index) => (
                                            <li className="flex items-center gap-2" key={index}>
                                                <CheckmarkCircle02SolidIcon
                                                    className={cn(
                                                        "size-4",
                                                        plan.isFeatured ? "text-white" : "text-(--text-sub-600)",
                                                    )}
                                                />
                                                <span
                                                    className={cn(
                                                        "text-paragraph-sm",
                                                        plan.isFeatured ? "text-white" : "text-(--text-sub-600)",
                                                    )}
                                                    dangerouslySetInnerHTML={{ __html: purify.sanitize(feature) }}
                                                />
                                            </li>
                                        ))}
                                    </ul>
                                </div>
                            );
                        })}
                    </div>
                </div>
            </div>
        </section>
    );
}

function PlanButton({ interval, plan }: { interval: string; plan: Plan }) {
    const { t } = useTranslation();
    const user = useUser();

    return (
        <Button.Root
            $style={plan.buttonStyle}
            $type={plan.buttonType}
            asChild
            className={cn(
                "w-full",
                plan.isFeatured && "bg-white text-primary hover:bg-brand-primary-600 hover:text-white",
            )}
        >
            {plan.code === "enterprise" || user ? (
                <a
                    href={
                        plan.code === "enterprise"
                            ? "mailto:sales@expensetrackr.app"
                            : routes.subscribe.url({
                                  query: {
                                      product_id:
                                          plan.productPriceId.onetime ||
                                          plan.productPriceId[interval as keyof typeof plan.productPriceId],
                                      code: plan.code,
                                  },
                              })
                    }
                >
                    {t(`pricing.${plan.code}.button_label`)}
                </a>
            ) : (
                <Link href={routes.register.url()}>{t(`pricing.${plan.code}.button_label`)}</Link>
            )}
        </Button.Root>
    );
}
