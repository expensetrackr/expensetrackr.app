import NumberFlow from "@number-flow/react";
import { format } from "date-fns";
import type Decimal from "decimal.js";
import * as React from "react";

import { totalBalancePeriods, useDashboardParams } from "#/hooks/use-dashboard-params.ts";
import { useTranslation } from "#/hooks/use-translation.ts";
import { type Trend } from "#/types/index.js";
import { cn } from "#/utils/cn.ts";
import { decimalFlowFormatter } from "#/utils/currency-formatter.ts";
import { decimalFormatter } from "#/utils/number-formatter.ts";
import ChartStepLine from "../chart-step-line.tsx";
import * as Badge from "../ui/badge.tsx";
import * as Select from "../ui/select.tsx";

type TotalBalanceWidgetProps = React.HTMLAttributes<HTMLDivElement> & {
    title: string;
    netWorth: Decimal.Value;
    netWorthSeries: {
        startDate: string;
        endDate: string;
        interval: string;
        trend: Trend;
        values: {
            date: string;
            dateFormatted: string;
            trend: Trend;
        }[];
    };
};

export function TotalBalanceWidget({ title, netWorth, netWorthSeries, className, ...rest }: TotalBalanceWidgetProps) {
    const { t, language } = useTranslation();
    const { totalBalancePeriod, setParams } = useDashboardParams();
    const netWorthFlow = decimalFlowFormatter({
        amount: netWorth,
        currency: "USD",
        language,
    });

    const dateFormatString = React.useMemo(() => {
        switch (netWorthSeries.interval) {
            case "day":
                return "MM/dd";
            case "month":
                return "MMM";
            default:
                return "MMM";
        }
    }, [netWorthSeries.interval]);

    return (
        <div
            className={cn(
                "relative flex flex-col rounded-16 bg-(--bg-white-0) p-5 pb-[18px] shadow-xs ring-1 ring-(--stroke-soft-200) ring-inset",
                className,
            )}
            {...rest}
        >
            <div className="flex h-full flex-col gap-5">
                <div className="flex items-start justify-between">
                    <div>
                        <div className="text-paragraph-sm text-(--text-sub-600)">{title}</div>
                        <div className="mt-1 flex items-center gap-2">
                            <div className="text-h5">
                                <NumberFlow format={netWorthFlow.format} value={netWorthFlow.value} />
                            </div>
                            <Badge.Root
                                $color={netWorthSeries.trend.favorableDirection === "up" ? "green" : "red"}
                                $size="md"
                                $style="light"
                            >
                                {netWorthSeries.trend.percentageChange}%
                            </Badge.Root>
                        </div>
                    </div>

                    <Select.Root
                        $size="xs"
                        $variant="compact"
                        onValueChange={(value) => setParams({ totalBalancePeriod: value }, { shallow: false })}
                        value={totalBalancePeriod}
                    >
                        <Select.Trigger>
                            <Select.Value />
                        </Select.Trigger>
                        <Select.Content align="center">
                            {totalBalancePeriods.map((item) => (
                                <Select.Item key={item} value={item}>
                                    {t(`common.periods.${item}`)}
                                </Select.Item>
                            ))}
                        </Select.Content>
                    </Select.Root>
                </div>

                <ChartStepLine
                    categories={["value"]}
                    data={netWorthSeries.values.map((value) => ({
                        date: value.date,
                        value: +decimalFormatter(value.trend.current, language, "USD"),
                    }))}
                    index="date"
                    xAxisProps={{
                        tickFormatter: (value) => format(value, dateFormatString).toLocaleUpperCase(),
                        tickMargin: 8,
                    }}
                    yAxisProps={{ hide: true }}
                />
            </div>
        </div>
    );
}
